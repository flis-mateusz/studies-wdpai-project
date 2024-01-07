import CustomContentLoaderController from './controllers/custom-loader.js';
import { DebounceSearchController } from './controllers/debounce-search-controller.js'
import { FilterSelectField } from './controllers/filter-select.js'
import { FetchController } from './controllers/fetch-controller.js';
import debounce from './controllers/debounce.js';

const FILTER_TYPES = {
    FEATURES: 'features',
    TYPES: 'types',
    SEARCH: 'search',
    OTHER: 'o',
};

class AnnouncementsSearch {
    constructor() {
        this.fetchController = new FetchController('/api_test', false);
        this.debounceSearch = new DebounceSearchController('search', 250);
        this.loader = new CustomContentLoaderController();
        this.loader.setupAbsolute();
        this.debounceSearch.addObserver(this.handleDebounceSearch);

        this.debounce = debounce(this.onDebounce, 500);

        this.filters = {
            [FILTER_TYPES.FEATURES]: {},
            [FILTER_TYPES.OTHER]: {},
            [FILTER_TYPES.TYPES]: new Set(),
            [FILTER_TYPES.SEARCH]: '',
        };

        this.initOptions('.options#animal-features .label', this.onAnimalFeatureChange, FILTER_TYPES.FEATURES, false);
        this.initOptions('.options#animal-types .label', this.onAnimalTypeChange, FILTER_TYPES.TYPES, true);
        this.initOptions('.options#other .label', this.onOtherChange, FILTER_TYPES.OTHER, false);

        this.outputElement = document.querySelector('.panel-elements');
        this.outputInfo = document.querySelector('.api-output');
        document.querySelector('.action-search').addEventListener('click', this.onDebounce);
        document.querySelector('.action-clear-search').addEventListener('click', () => {
            this.debounceSearch.setInputValue('');
            this.handleDebounceSearch('')
        });
    }

    set(type, id, value) {
        this[type][id].set(value)
        this.onOptionChange(type, id, value)
    }

    initOptions(selector, onChange, type, limitedStates) {
        this[type] = {};
        document.querySelectorAll(selector).forEach((element) => {
            let option = new FilterSelectField(element, limitedStates);
            option.onUpdate = onChange;
            this[type][option.id] = option;
        });
    }

    onAnimalFeatureChange = (id, value) => this.onOptionChange(FILTER_TYPES.FEATURES, id, value);
    onAnimalTypeChange = (id, value) => this.onOptionChange(FILTER_TYPES.TYPES, id, value);
    onOtherChange = (id, value) => {
        switch (id) {
            case 'my':
                if (value != 0) {
                    this.set(FILTER_TYPES.OTHER, 'favourite', 0)
                }
                break
            case 'favourite':
                if (value != 0) {
                    this.set(FILTER_TYPES.OTHER,'my', 0)
                }
                break
        }
        this.onOptionChange(FILTER_TYPES.OTHER, id, value)
    };

    onOptionChange = (type, id, value) => {
        const filterSet = this.filters[type];

        if (type === FILTER_TYPES.TYPES) {
            if (value == '2' || value == '1') {
                filterSet.add(id);
            } else if (value == '0') {
                filterSet.delete(id);
            }
        } else {
            if (value == '2' || value == '1') {
                this.filters[type][id] = value;
            } else if (value == '0') {
                delete this.filters[type][id];
            }
        }

        this.updateQueryParams();
    }

    showOutputInfo(text, error = false) {
        this.outputInfo.innerText = text;
        this.outputInfo.classList.toggle('error', error);
    }

    onDebounce = async () => {
        this.loader.show();
        this.loader.timeWait(300).then(this.search)
    }

    search = async () => {
        this.showOutputInfo('', false);
        this.fetchController.abort();

        const params = new URLSearchParams(window.location.search)
        this.fetchController.setUrl(`/query_announcements?${params.toString()}`);
        try {
            const data = await this.fetchController.get();
            if (!data) {
                this.setAnnouncements('');
                this.showOutputInfo('Nie znaleziono ogłoszeń');
            } else {
                this.setAnnouncements(data)
            }
        } catch (error) {
            this.showOutputInfo(error.message, true);
        }
        this.loader.hide();
    }
    setAnnouncements(announcements) {
        this.outputElement.innerHTML = announcements;
    }

    updateQueryParams() {
        this.debounce();

        const searchParams = new URLSearchParams();
        Object.entries(this.filters).forEach(([type, values]) => {
            if (type === FILTER_TYPES.SEARCH && values) {
                searchParams.set(FILTER_TYPES.SEARCH, values);
            } else if (type === FILTER_TYPES.TYPES && values.size > 0) {
                searchParams.set(type, Array.from(values).join(';'));
            } else {
                const paramString = Object.entries(values)
                    .map(([id, value]) => `${id}-${value}`)
                    .join(';');
                if (paramString) {
                    searchParams.set(type, paramString);
                }
            }
        });

        window.history.replaceState({}, '', `${window.location.pathname}?${searchParams.toString()}`);
    }

    setFromURL() {
        const params = new URLSearchParams(window.location.search);

        Object.values(FILTER_TYPES).forEach(type => {
            const param = params.get(type);
            if (!param) return;

            if (type === FILTER_TYPES.SEARCH) {
                this.filters[type] = param;
                this.debounceSearch.setInputValue(param);
            } else {
                const values = param.split(';');
                values.forEach(value => {
                    if (type === FILTER_TYPES.TYPES) {
                        this.filters[type].add(value);
                        this[type][value].set(value);
                    } else {
                        const [id, val] = value.split('-');
                        this.filters[type][id] = val;
                        this.applyOptionFilter(this[type], id, val);
                    }
                });
            }
        });

        this.onDebounce();
    }

    applyOptionFilter(filterSet, id, value) {
        const optionObject = filterSet[id];
        if (optionObject) {
            optionObject.set(value);
        }
    }

    handleDebounceSearch = (query) => {
        this.filters[FILTER_TYPES.SEARCH] = query;
        this.updateQueryParams();
    }
}

const announcementsSearch = new AnnouncementsSearch();
announcementsSearch.setFromURL();
