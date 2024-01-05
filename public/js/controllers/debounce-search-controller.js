import { FetchController } from './fetch-controller.js';

class DebounceSearchController {
    constructor(id, timeout) {
        this.parentElement = document.querySelector(`#${id}`);
        this.inputElement = this.parentElement.querySelector('input.search-input');
        this.timeout = timeout;

        this.debounceSearch = this.debounce(this.#update, this.timeout);
        this.addEventListeners();

        this.observers = [];
    }

    addObserver(observer) {
        this.observers.push(observer);
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

    addEventListeners() {
        this.inputElement.addEventListener('keyup', (e) => {
            this.onInput();
            this.debounceSearch(e);
        });
    }

    #update = async () => {
        let query = this.inputElement.value.toLowerCase().trim();
        this.parentElement.classList.add('loading');

        await this.#search(query);

        this.parentElement.classList.remove('loading');
    }

    async #search(query) {
        for (let observer of this.observers) {
            await observer(query)
        }
    }

    onInput() { }
}

class DebounceSelectSearchController extends DebounceSearchController {
    constructor(id, url, timeout, preLoadedData) {
        super(id, timeout);
        this.url = url;
        this.setupElements(id);
        this.preLoadedData = preLoadedData;
        this.fetchController = new FetchController(this.url);
        if (this.preLoadedData) {
            this.displayResults(this.preLoadedData);
        }
        this.observers.push(this.search.bind(this));
    }

    setupElements(id) {
        this.outputElement = this.parentElement.querySelector('.search-results');
        this.targetInputElement = this.parentElement.querySelector('input.target-input');
    }

    displayResults(data) {
        this.outputElement.innerHTML = '';
        data?.forEach(item => this.createResultItem(item));
    }

    createResultItem(object) {
        for (let [key, value] of Object.entries(object)) {
            this.matchAndDisplayResult(key, value);
            this.appendResultElement(key, value);
        }
    }

    matchAndDisplayResult(key, value) {
        if (value.toLowerCase().trim() === this.inputElement.value.toLowerCase().trim()) {
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

    async fetchSearchResults(query) {
        await this.fetchController.post({ 'search': query })
            .then((data) => {
                this.displayResults(data.data)
            })
            .catch();
    }

    async search(query) {
        if (!this.url) {
            this.filterLocalData(query);
        } else {
            await this.fetchSearchResults(query);
        }
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

    handleOptionSelect(optionId, optionName) {
        this.setTargetInputValue(optionId);
        this.inputElement.value = optionName;
        this.targetInputElement.dispatchEvent(new Event('change'));
        this.parentElement.classList.add('selected');
        this.inputElement.classList.add('valid');
    }

    setTargetInputValue(value) {
        this.targetInputElement.value = value;
    }

    onInput() {
        this.setTargetInputValue('');
        this.targetInputElement.dispatchEvent(new Event('change'));
        this.parentElement.classList.remove('selected');
        this.inputElement.classList.remove('valid');
    }
}

export { DebounceSearchController, DebounceSelectSearchController };
