class FilterSelectField {
    constructor(element, limitedStates = false) {
        this._id = element.dataset.id;
        this.element = element;
        this.element?.addEventListener('click', this.handleChange);
        this.state = '';
        this.limitedStates = limitedStates;
    }

    get id() {
        return this._id;
    }

    changeClass = () => {
        this.element.classList.remove('yes', 'no');
        if (this.state) {
            this.element.classList.add(this.state);
        }
    }

    handleChange = () => {
        if (this.limitedStates) {
            this.state = this.state == 'yes' ? '' : 'yes';
        } else {
            this.state = this.state == 'yes' ? 'no' : (this.state == 'no' ? '' : 'yes');
        }
        this.changeClass();
        this.onUpdate(this._id, this.state == 'yes' ? 2 : this.state == 'no' ? 1 : 0);
    }

    set(value) {
        if (this.limitedStates) {
            this.state = value ? 'yes' : '';
        } else {
            this.state = value == 2 ? 'yes' : value == 1 ? 'no' : '';
        }
        this.changeClass();
    }

    disable() {
        this.element.classList.add('disabled');
    }
    
    enable() {
        this.element.classList.remove('disabled');
    }

    onUpdate = (id, value) => { }
}

export { FilterSelectField };
