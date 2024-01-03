class CustomContentLoaderController {
    constructor(searchRoot) {
        if (searchRoot) {
            this.loader = searchRoot.querySelector('.custom-loader-container');
        } else {
            this.loader = document.querySelector('.custom-loader-container');
        }
    }

    setupDarker() {
        this.loader.classList.add('darker');
    }

    setupFixed() {
        this.loader.classList.remove('absolute');
        this.loader.classList.add('fixed');
    }

    setupAbsolute() {
        this.loader.classList.remove('fixed');
        this.loader.classList.add('absolute');
    }

    setupAbsoluteCenteredPOV() {
        this.loader.classList.remove('fixed');
        this.loader.classList.add('absolute', 'absolute-centered-pov');
    }

    show() {
        this.loader.classList.remove('hidden');
    }

    hide = () => {
        this.loader.classList.add('hidden');
        this.reset();
    }

    setSucess(minimize) {
        this.loader.classList.add('success');
        if (minimize) {
            this.loader.classList.add('minimize');
        }
    }

    completeLoadingAsync(time = 700, value = null) {
        this.setSucess()
        return new Promise(resolve => setTimeout(resolve, time, value));
    }

    timeWait(time = 700, value = null) {
        return new Promise(resolve => setTimeout(resolve, time, value));
    }

    reset() {
        this.loader.classList.remove('success');
        this.loader.classList.remove('minimize');
    }
}

export default CustomContentLoaderController;