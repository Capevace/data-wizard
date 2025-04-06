import Alpine from 'alpinejs'
import Zoomable from '@benbjurstrom/alpinejs-zoomable'
import Clipboard from "@ryangjchandler/alpine-clipboard"

Alpine.plugin(Clipboard)
Alpine.plugin(Zoomable)

Alpine.start()

console.info('Alpine initialized')
