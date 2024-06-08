import { Controller } from '@hotwired/stimulus';
import ClassicEditor from '../js/ckeditor5/build/ckeditor';

// /* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['txt'];

    count = 0;

    timer;

    static values = {
        folder: Number,
        social: String,
        pageId: Number,
    };

    editor;

    FbConfig = {
        allowedContent: 'p b i; a[!href]',
        mention: {
            feeds: [
                {
                    marker: '@',
                    feed: this.getFeedItems.bind(this),
                    minimumCharacters: 3,
                    itemRenderer: this.getItemRenderer.bind(this),
                },
            ],
        },
        autoParagraph: false,
    };

    defaultConfig = {
        allowedContent: 'p b i; a[!href]',
    };

    async connect() {
        document.addEventListener('updateCkeditor', (e) => {
            this.socialValue = e.detail.social;
            this.pageIdValue = e.detail.page;
            this.reloadEditor();
        });

        if (this.count === 0) {
            const config = this.socialValue === 'Facebook' || this.socialValue === 'Linkedin' ? this.FbConfig : this.defaultConfig;
            config.heading = {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    {
                        model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1',
                    },
                    {
                        model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2',
                    },
                    {
                        model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3',
                    },
                ],
            };

            await ClassicEditor.create(this.txtTarget, config)
                .then((editor) => {
                    this.editor = editor;
                    editor.editing.view.document.on('clipboardInput', (evt, data) => {
                        const { dataTransfer } = data;
                        let content = dataTransfer.getData('text/html');
                        if (content.length < 1) {
                            content = dataTransfer.getData('text/plain');
                        }
                        data.content = this.editor.data.htmlProcessor.toView(content);
                    });
                    let data = new CustomEvent('editorChange', { detail: { data: this.editor.getData() } });
                    document.dispatchEvent(data);
                    // Envoie de l'event pour la validation du formulaire
                    document.querySelector('.ck').addEventListener('focusout', () => {
                        data = new CustomEvent('editorChange', { detail: { data: this.editor.getData() } });
                        document.dispatchEvent(data);
                    });
                })
                .catch((err) => console.error(err.stack));
        }
    }

    async reloadEditor() {
        let data = '';
        if (this.editor) {
            data = this.editor.getData();
            await this.editor.destroy();
        }
        await ClassicEditor.create(this.txtTarget, this.socialValue === 'Facebook' || this.socialValue === 'Linkedin' ? this.FbConfig : {})
            .then((editor) => {
                this.editor = editor;
                this.editor.setData(data);
                editor.editing.view.document.on('clipboardInput', (evt, data) => {
                    const { dataTransfer } = data;
                    let content = dataTransfer.getData('text/html');
                    if (content.length < 1) {
                        content = dataTransfer.getData('text/plain');
                    }
                    data.content = this.editor.data.htmlProcessor.toView(content);
                });
                document.querySelector('.ck').addEventListener('focusout', () => {
                    const dataSend = new CustomEvent('editorChange', { detail: { data: this.editor.getData() } });
                    document.dispatchEvent(dataSend);
                });
            })
            .catch((err) => {
                console.error(err.stack);
            });
    }

    getItemRenderer(item) {
        const itemElement = document.createElement('div');
        const loc = item.location;
        const city = loc ? loc.city : '';
        let url = '';
        if (this.socialValue === 'Linkedin') {
            url = item.picture ? item.picture : companyAvatar;
        } else {
            url = item.picture ? item.picture : 'https://fakeimg.pl/40x40/';
        }
        itemElement.setAttribute('data-user-id', item.id);
        itemElement.setAttribute('data-user-name', item.name);
        itemElement.innerHTML = `
    <div style="display: flex; align-items: center;">
      <img src="${url}" alt="Nom de la personne" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;">
      <div style="font-family: Arial, sans-serif;">
<h4 style="font-size: 12px; margin: 0;">${item.name} ${city ? ` | ${city}` : ''}</h4>
      </div>
    </div>
    `;
        return itemElement;
    }

    async txtTargetDisconnected() {
        this.count = 1;
        if (this.editor) {
            await this.editor.destroy();
        }
    }
}