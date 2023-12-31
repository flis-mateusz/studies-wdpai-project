import { FetchController } from './fetch-controller.js';

class DebounceSearchController {
    constructor(id, url, timeout, preLoadedData) {
        this.setupElements(id);
        this.url = url;
        this.timeout = timeout;
        this.preLoadedData = preLoadedData;
        this.fetchController = new FetchController(this.url);
        this.debounceSearch = this.debounce(this.search.bind(this), this.timeout);

        this.addEventListeners();

        if (this.preLoadedData) {
            this.displayResults(this.preLoadedData);
        }
    }

    setupElements(id) {
        this.element = document.querySelector(`#${id}`);
        this.outputElement = this.element.querySelector('.search-results');
        this.targetInputElement = this.element.querySelector('input.target-input');
        this.inputSearchElement = this.element.querySelector('input.search-input');
    }

    addEventListeners() {
        this.inputSearchElement.addEventListener('keyup', (e) => {
            this.resetSearch();
            this.debounceSearch(e);
        });
    }

    debounce(func, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => {
                func(...args);
            }, delay);
        };
    }

    displayResults(data) {
        this.outputElement.innerHTML = '';
        data?.forEach(item => this.createResultItem(item));
    }

    createResultItem(object) {
        for (let key in object) {
            if (object.hasOwnProperty(key)) {
                let value = object[key];
                this.matchAndDisplayResult(key, value);
                this.appendResultElement(key, value);
            }
        }
    }

    matchAndDisplayResult(key, value) {
        if (value.toLowerCase().trim() === this.inputSearchElement.value.toLowerCase().trim()) {
            this.handleOptionSelect(key, value);
        }
    }

    appendResultElement(key, value) {
        let element = document.createElement('div');
        element.setAttribute('data-id', key);
        element.textContent = value;
        this.outputElement.appendChild(element);
        element.addEventListener('click', () => {
            this.handleOptionSelect(key, value);
        });
    }

    async search() {
        let query = this.inputSearchElement.value.toLowerCase().trim();
        this.element.classList.add('loading');

        if (!this.url) {
            this.filterLocalData(query);
        } else {
            await this.fetchSearchResults(query);
        }

        this.element.classList.remove('loading');
    }

    filterLocalData(query) {
        Array.from(this.outputElement.children).forEach(child => {
            let value = child.textContent.toLowerCase().trim();
            child.style.display = value.includes(query) ? "" : "none";
            if (query === value) {
                this.handleOptionSelect(child.getAttribute('data-id'), child.textContent);
            }
        });
    }

    async fetchSearchResults(query) {
        const data = await this.fetchController.post({ 'search': query });
        this.displayResults(data.data);
    }

    handleOptionSelect(optionId, optionName) {
        this.setTargetInputValue(optionId);
        this.inputSearchElement.value = optionName;
        this.element.classList.add('selected');
    }

    setTargetInputValue(value) {
        this.targetInputElement.value = value;
    }

    resetSearch() {
        this.setTargetInputValue('');
        this.element.classList.remove('selected');
    }
}

export default DebounceSearchController;
