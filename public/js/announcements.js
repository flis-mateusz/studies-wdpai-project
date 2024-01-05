import { DebounceSearchController } from './controllers/debounce-search-controller.js'

class AnnouncementsSearch {
    constructor() {
        this.debounceSearch = new DebounceSearchController('search', 800);
        this.debounceSearch.addObserver(this.handleDebounceSearch)
    }
    handleDebounceSearch(query) {
        console.log(query);
    }
}

new AnnouncementsSearch();