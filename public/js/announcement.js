import CustomContentLoaderController from './controllers/custom-loader.js';
import { FetchController } from './controllers/fetch-controller.js';
import { redirectToTargetOrDefault } from './utils.js'

class Announcement {
    constructor(id) {
        this.id = id;

        this.loader = new CustomContentLoaderController();
        this.loader.setupAbsoluteCenteredPOV()
        this.output = document.querySelector('span.input-error');
        this.refferer = document.referrer;

        this.actionApprove = document.querySelector('.action-approve')
        this.actionApprove?.addEventListener('click', this.approve.bind(this))
        this.actionReport = document.querySelector('.action-report')
        this.actionReport?.addEventListener('click', this.report.bind(this))
        document.querySelector('.action-edit')?.addEventListener('click', this.edit.bind(this))
        document.querySelector('.action-delete')?.addEventListener('click', this.delete.bind(this))
        this.actionLike = document.querySelector('.action-like')
        this.actionLike?.addEventListener('click', this.like.bind(this))
    }

    showOutput(text) {
        this.output.parentElement.classList.remove('hidden');
        this.output.innerText = text
    }

    hideOutput() {
        this.output.parentElement.classList.add('hidden');
    }

    async beforeSend() {
        this.loader.show();
        this.hideOutput();
        await this.loader.timeWait(300);
    }

    handleResponse(data) {
        switch (data.data) {
            case 'approved':
                this.actionApprove.classList.add('hidden');
                break;
            case 'reported':
                this.actionReport.classList.add('hidden');
                break;
            case 'liked':
                this.actionLike.classList.add('liked');
                break;
            case 'unliked':
                this.actionLike.classList.remove('liked');
                break;
        }
        this.loader.completeLoadingAsync().then(() => {
            if (data.data?.redirect_url) {
                window.location.href = data.data.redirect_url
                return
            }
            this.loader.hide()
        })
    }

    handleError(error) {
        this.loader.hide()
        this.showOutput(error.message);
    }

    async performAction(apiEndpoint, method = 'post') {
        await this.beforeSend();
        new FetchController(apiEndpoint)[method]({ 'id': this.id, 'referrer': this.refferer ? this.refferer : null })
            .then(data => this.handleResponse(data))
            .catch(error => this.handleError(error));
    }

    approve(e) {
        this.performAction('/api_announcement_approve');
    }

    report(e) {
        this.performAction('/api_announcement_report');
    }

    edit(e) {
        this.beforeSend()
        window.location.href = '/edit/' + this.id;
    }

    delete(e) {
        this.performAction('/api_announcement_delete');
    }

    like(e) {
        this.performAction('/api_announcement_like');
    }
}

export default Announcement;