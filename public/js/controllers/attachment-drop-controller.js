class AttachmentDragDropController {
    constructor(element, allowedAmount = 1) {
        this.dropArea = element;
        this.allowedAmount = allowedAmount;

        this.input = this.dropArea.querySelector('input[type="file"]')
        this.preview = this.dropArea.querySelector('.attachment-preview')

            ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                this.dropArea.addEventListener(eventName, this.preventDefaults)
                document.addEventListener(eventName, this.preventDefaults)
            })

            ;['dragenter', 'dragover'].forEach(eventName => {
                this.dropArea.addEventListener(eventName, this.highlight)
            })

            ;['dragleave', 'drop'].forEach(eventName => {
                this.dropArea.addEventListener(eventName, this.unhighlight)
            })

        this.output = this.dropArea.querySelector('span.error-output')
        this.input.addEventListener('change', this.hanleInputChange)
        this.dropArea.addEventListener('drop', this.handleDrop)
    }

    preventDefaults(e) {
        e.preventDefault()
        e.stopPropagation()
    }

    highlight = (e) => {
        this.dropArea.classList.add('active')
    }

    unhighlight = (e) => {
        this.dropArea.classList.remove('active')
    }

    handleDrop = (e) => {
        if (e.dataTransfer.files.length > this.allowedAmount) {
            this.handleError('Możesz przesłać maksymalnie ' + this.allowedAmount + ' plików')
            return
        }
        this.handleFilesUploaded(e.dataTransfer.files)
    }

    handleFilesUploaded(files) {
        this.output.classList.remove('visible')
        this.preview.classList.remove('active')
        this.preview.innerHTML = ''

        const dataTransfer = new DataTransfer();
        let invalidFilesCount = 0;

        Array.from(files).forEach(file => {
            if (this.validateFile(file)) {
                this.handlePreview(file);
                dataTransfer.items.add(file);
            } else {
                invalidFilesCount++;
            }
        });
        if (invalidFilesCount > 0) {
            this.handleError('Nieprawidłowe pliki: ' + invalidFilesCount)
        }

        this.input.files = dataTransfer.files
    }

    hanleInputChange = (e) => {
        this.handleFilesUploaded(e.target.files)
    }

    handlePreview(file) {
        let newChild = document.createElement('img')
        newChild.classList.add('attachment')

        let reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onloadend = () => {
            newChild.src = reader.result
            this.preview.appendChild(newChild)
        }
    }

    getFiles() {
        return this.input.value
    }

    handleError(e) {
        this.output.innerText = e
        this.output.classList.add('visible')
    }

    validateFile(file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (validTypes.includes(file.type)) {
            return true;
        } else {
            return false;
        }
    }
}

export default AttachmentDragDropController;