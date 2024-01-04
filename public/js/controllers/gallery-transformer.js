export default class GalleryTransformerController {
    constructor(triggerSelector) {
        if (triggerSelector instanceof Element) {
            this.parent = triggerSelector;
        } else {
            this.parent = document.querySelector(triggerSelector);
        }
        this.gallery = null;
        this.originalParent = null;

        this.parent?.addEventListener('click', this.openGallery.bind(this));
    }

    openGallery(event) {
        if (this.gallery) {
            this.closeGallery();
        }

        if (this.parent.classList.contains('hidden')) {
            this.parent.classList.remove('hidden');
        }

        if (this.parent.tagName === 'IMG') {
            this.createGalleryForImage();
        } else {
            this.createGalleryForNonImage();
        }
    }

    createGalleryForImage() {
        this.originalParent = this.parent.parentNode;

        const container = document.createElement('div');
        container.classList.add('gallery');

        container.appendChild(this.parent);  //.cloneNode()
        container.addEventListener('click', this.closeGallery.bind(this));

        this.gallery = container;
        this.originalParent.appendChild(container);
    }

    createGalleryForNonImage() {
        let imgUrl = this.extractImageUrl();
        if (!imgUrl) {
            console.warn('Cannot create gallery when no image is set');
            return;
        }

        const container = document.createElement('div');
        container.classList.add('gallery');

        const newImg = document.createElement('img');
        newImg.src = imgUrl;

        container.appendChild(newImg);
        container.addEventListener('click', this.closeGallery.bind(this));

        this.gallery = container;
        this.parent.appendChild(this.gallery);
    }

    extractImageUrl() {
        return this.parent.style.backgroundImage
            ?.replace('url(', '')
            .replace(')', '')
            .replace(/"/g, '');
    }

    closeGallery(event) {
        event?.stopPropagation();

        if (this.parent.tagName === 'IMG') {
            this.originalParent.appendChild(this.parent);
        }

        this.gallery?.remove();
        this.gallery = null;
    }
}
