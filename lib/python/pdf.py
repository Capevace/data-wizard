import fitz
from PIL import Image
import os
from pdf2image import convert_from_path
import shutil

class Page:
    def __init__(self, number: int, text: str, images: list):
        self.number = number
        self.text = text
        self.images = images

    def __str__(self):
        return f"Page {self.number}: {self.text}"

    def to_dict(self):
        return {
            "number": self.number,
            "text": self.text.decode("utf8"),
            "images": [image.to_dict() for image in self.images]
        }

class Image:
    def __init__(self, page: int, number: int, path: str, x: float, y: float, width: float, height: float):
        self.page = page
        self.number = number
        self.path = path
        self.x = x
        self.y = y
        self.width = width
        self.height = height

    def bbox(self):
        return fitz.Rect(self.x, self.y, self.x + self.width, self.y + self.height)

    def text_bbox(self):
        return fitz.Rect(self.x, self.y, self.x + 100, self.y + 70)

    def to_dict(self):
        return {
            "page": self.page,
            "number": self.number,
            "path": self.path,
            "x": self.x,
            "y": self.y,
            "width": self.width,
            "height": self.height
        }

def draw_red_borders_around_images(doc, images):
    for page in doc:
        img_list = page.get_images(full=True)

        # every image where page is page.number
        page_images = [image for image in images if image.page == page.number]

        for image in page_images:
            page.draw_rect(image.bbox(), color=(1, 0, 0), width=10)

            # draw "IMAGE" in the middle of the image
            page.draw_rect(image.text_bbox(), color=(1,0,0), fill=(1, 0, 0))

            shape = page.new_shape()
            shape.insert_textbox(image.text_bbox(), str(image.number), fontsize=40, color=(1, 1, 1), align=1)
            shape.finish(fill=(1, 0, 0))
            shape.commit()

def save_image(doc, image, path):
    xref = image[0]
    # extract the image bytes
    base_image = doc.extract_image(xref)
    image_bytes = base_image["image"]

    # you can save the image to disk with open()
    with open(path, "wb") as img_file:
        img_file.write(image_bytes)

def save_pages_as_files(doc, full_text_path, pages_marked_dir, pages_txt_dir):
    texts = []

    for (page, pageIndex) in zip(doc, range(len(doc))):
        text_path = pages_marked_dir + f"/page{pageIndex + 1}.txt"
        image_path = pages_marked_dir + f"/page{pageIndex + 1}.jpg"

        text = page.get_text().encode("utf8")
        texts.append(text)

        with open(text_path, "wb") as text_file:
            text_file.write(text)

        page = doc.load_page(pageIndex)
        pix = page.get_pixmap()
        pix.save(image_path)

    # join texts with "---- Page X ----" header for each page with number of page replaced
    full_text = b""
    pageIndex = 0

    for text in texts:
        pageIndex += 1
        full_text += f"---- Page {pageIndex} ----\n".encode("utf8")
        full_text += text
        full_text += b"\n\n"

    with open(full_text_path, "wb") as text_file:
        text_file.write(full_text)

# def resize_pages():
#     pages_path = artifact_dir + f"/pages_marked/"
#
#     for page in os.listdir(pages_path):
#         image = Image.open(pages_path + page)
#         image = image.resize((int(image.width / 2), int(image.height / 2)))
#         image.save(pages_path + page)

def extract_pages(doc, images_dir):
    images = []

    pages = []
    for (page, pageIndex) in zip(doc, range(len(doc))):
        text = page.get_text().encode("utf8")
        img_list = page.get_images(full=True)
        page_images = []

        for image in img_list:
            # select the image referencing the old image (hope you know how to identify it!)
            # Each image looks like: (1315, 0, 1945, 1004, 8, 'DeviceRGB', '', 'Im1', 'DCTDecode', 0)
            # first entry is xref, etc.
            bbox = page.get_image_bbox(image)  # where the old image lives
            number = len(images) + 1

            image_path = images_dir + f"/image{number}.jpg"
            save_image(doc, image, image_path)

            image = Image(page.number, number, image_path, bbox.x0, bbox.y0, bbox.width, bbox.height)
            page_images.append(image)
            images.append(image)

        pages.append(Page(page.number, text, page_images))

    return pages