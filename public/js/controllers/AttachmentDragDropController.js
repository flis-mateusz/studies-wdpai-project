class AttachmentDragDropController {
    constructor(element, allowedAmount = 1) {
        this.dropArea = element;
        this.allowedAmount = allowedAmount;

        this.input = this.dropArea.querySelector('input[type="file"]')
        this.preview = this.dropArea.querySelector('.attachment-preview')

            ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                this.dropArea.addEventListener(eventName, this.preventDefaults.bind(this))
                document.addEventListener(eventName, this.preventDefaults.bind(this))
            })

            ;['dragenter', 'dragover'].forEach(eventName => {
                this.dropArea.addEventListener(eventName, this.highlight.bind(this))
            })

            ;['dragleave', 'drop'].forEach(eventName => {
                this.dropArea.addEventListener(eventName, this.unhighlight.bind(this))
            })

        this.output = this.dropArea.querySelector('span.error-output')
        this.input.addEventListener('change', this.hanleInputChange.bind(this))
        this.dropArea.addEventListener('drop', this.handleDrop.bind(this))
    }

    preventDefaults(e) {
        e.preventDefault()
        e.stopPropagation()
    }

    highlight(e) {
        this.dropArea.classList.add('active')
    }

    unhighlight(e) {
        this.dropArea.classList.remove('active')
    }

    handleDrop(e) {
        if (e.dataTransfer.files.length > this.allowedAmount) {
            this.handleError('Możesz przesłać maksymalnie ' + this.allowedAmount + ' plików')
            return
        }
        this.handleFilesUploaded(e.dataTransfer.files)
    }

    handleFilesUploaded(files) {
        this.output.classList.remove('visible')
        this.input.files = files
        this.preview.classList.remove('active')
        this.preview.innerHTML = ''

        Array.from(files).forEach(file => {
            this.handlePreview(file)
        })
    }

    hanleInputChange(e) {
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
}

export default AttachmentDragDropController;