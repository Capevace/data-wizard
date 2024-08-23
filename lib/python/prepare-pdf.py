import fitz
from PIL import Image
import os
from pdf2image import convert_from_path
import shutil
from pdf import extract_pages, save_pages_as_files, save_image, draw_red_borders_around_images
import json


if len(os.sys.argv) < 3:
    print("Usage: python prepare-pdf.py <artifact-dir> <pdf-path>")

class JsonOutput:
    paths = {}
    pages = []
    error = None

    def to_dict(self):
        # output { paths: {} }

        try:
            pages = [page.to_dict() for page in self.pages]
        except Exception as e:
            self.error = str(e)

        if self.error:
            return {"error": self.error}
        else:
            return {
                "paths": self.paths,
                "pages": pages
            }

    def to_json(self):
        return json.dumps(self.to_dict(), indent=4)

class Metadata:
    name: str
    mimetype: str
    extension: str

    def to_dict(self):
        return { "name": self.name, "mimetype": self.mimetype, "extension": self.extension }

    def to_json(self):
        return json.dumps(self.to_dict(), indent=4)

json_output = JsonOutput()

artifact_dir = os.sys.argv[1]
temp_pdf_path = os.sys.argv[2]

pdf_path = artifact_dir + "/source.pdf"
marked_pdf_path = artifact_dir + "/marked.pdf"
images_dir = artifact_dir + "/images"
pages_marked_dir = artifact_dir + "/pages_marked"
pages_txt_dir = artifact_dir + "/pages_txt"
full_text_path = artifact_dir + "/source.txt"
metadata_path = artifact_dir + "/metadata.json"

metadata = Metadata()
metadata.name = os.path.basename(temp_pdf_path)
metadata.mimetype = "application/pdf"
metadata.extension = "pdf"

json_output.paths["artifact_dir"] = artifact_dir
json_output.paths["temp_pdf_path"] = temp_pdf_path
json_output.paths["pdf_path"] = pdf_path
json_output.paths["marked_pdf_path"] = marked_pdf_path
json_output.paths["images_dir"] = images_dir
json_output.paths["pages_marked_dir"] = pages_marked_dir
json_output.paths["pages_txt_dir"] = pages_txt_dir
json_output.paths["full_text_path"] = full_text_path

try:
    # create the artifact dir if it doesn't exist (-m option)
    os.makedirs(artifact_dir, exist_ok=True)
    os.makedirs(images_dir, exist_ok=True)
    os.makedirs(pages_marked_dir, exist_ok=True)
    os.makedirs(pages_txt_dir, exist_ok=True)

    # copy the temp pdf to the artifact dir
    shutil.copy(temp_pdf_path, pdf_path)

    # write the metadata
    with open(metadata_path, "w") as f:
        f.write(metadata.to_json())

    # open the pdf
    doc = fitz.open(pdf_path)

    # extract the pages (and save any images in the images dir)
    pages = extract_pages(doc, images_dir)

    # flatten images
    images = [image for page in pages for image in page.images]

    # filter width or height > 200
    filtered_images = [image for image in images if image.width > 200 and image.height > 200]

    draw_red_borders_around_images(doc, filtered_images)

    doc.save(marked_pdf_path)

    save_pages_as_files(doc, full_text_path, pages_marked_dir, pages_txt_dir)
except Exception as e:
    json_output.error = str(e)

json_output.pages = pages

print(json_output.to_json())