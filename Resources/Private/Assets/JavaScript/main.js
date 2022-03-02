import DocumentService from 'DocumentService';
import Modal from 'Modal';
import Color from 'color';
import Sortable from 'sortablejs/modular/sortable.core.esm.js';
import {ColorPickerElement} from './colorPicker';
import {createColorElement, createElement, getHexValue, isLightColor} from "./helper";
import css from "./colorPalette.css";

class ColorPaletteInputElement extends HTMLElement {

    static get observedAttributes() {
        return ['ref'];
    }

    constructor() {
        super();
        // build shadow root with css then apply colors
        this.colorPalette = createElement('div', {class: 'color-palette'});
        const style = createElement('style', {textContent: css});
        const root = this.attachShadow({mode: 'open'});
        root.append(style, this.colorPalette);

        // render based on mode
        this.mode = this.getAttribute('mode') || null;
        if (this.mode === 'preview') {
            console.debug('renderPreview');
            this.renderPreview();
        } else {
            console.debug('renderPalette (mode: ' + this.mode + ')');
            this.renderPalette();
        }
    }

    get ref() {
        return this._ref;
    }

    set ref(ref) {
        if (ref === null) return;
        this._ref = ref;
        this._input = document.getElementById(this.ref);
        console.debug('reference: ' + this._ref, this._input);
        this.renderPaletteContent();
    }

    get value() {
        return this._input.value.split('|').filter(x => !!x).map((textColor) => Color(textColor));
    }

    set value(colorPalette) {
        console.debug('old-value:', this._input);
        this._input.value = [...colorPalette.querySelectorAll('.color')].map((item) => Color(item.style.backgroundColor)).join('|');
        console.debug('new-value:', this._input);
    }

    connectedCallback() {
        // temporary fix for firefox => https://bugzilla.mozilla.org/show_bug.cgi?id=1502814
        this.__proto__ = customElements.get('color-palette').prototype;
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'ref') {
            this.ref = newValue; // update palette if input reference is changed
        }
    }

    renderPreview() {
        this.colorPalette.classList.add('preview');
        this.colorPalette.addEventListener('click', () => {
            // create modal on click with listener to re-render when modal gets closed
            const colorPalette = createElement('color-palette', {ref: this.ref, append: this.childNodes});
            const modalContent = createElement('div', {class: 'color-palette-modal', append: [colorPalette]});
            const modal = Modal.advanced({
                title: 'Color Palette',
                content: modalContent,
                size: Modal.sizes.large,
                additionalCssClasses: ['color-palette-modal-wrapper']
            });

            console.debug('paletteModalOpened', this.colorPalette);
            modal.on('hide.bs.modal', () => {
                this.renderPaletteContent(); // re-render colors on close
                console.debug('paletteModalClosed', this.colorPalette);
            });
        });
    }

    renderPalette() {
        // create color picker component and event listener
        const colorPicker = createElement('color-picker', {style: 'display:none'});
        colorPicker.addEventListener('colorChanged', (event) => {
            this.currentColorItem.classList.toggle('light', isLightColor(event.detail.color));
            this.currentColorItem.style.backgroundColor = event.detail.color;
            this.currentColorItem.querySelector('.edit').textContent = getHexValue(event.detail.color);
            this.value = this.colorPalette;
            console.debug('colorChanged: ' + event.detail.color, this.currentColorItem);
        });
        this.shadowRoot.appendChild(colorPicker);

        this.colorPalette.classList.add('editor');
        this.colorPalette.addEventListener('click', (event) => {
            const currentColor = event.target.closest('.color');
            const currentAction = event.target.closest('.action');
            if (currentAction === null || currentColor === null) return;

            // check which action has been clicked
            if (currentAction.classList.contains('add') === true && event.target.closest('svg') !== null) {
                // check color before and after to find sufficient new color => then add after current
                let newColor = Color(currentColor.style.backgroundColor);
                if (typeof currentColor.nextSibling?.style.backgroundColor !== 'undefined') {
                    newColor = newColor.mix(Color(currentColor.nextSibling.style.backgroundColor));
                } else {
                    newColor = newColor.darken(0.1);
                }
                // add value and set input value again
                currentColor.parentNode.insertBefore(createColorElement(newColor, true), currentColor.nextSibling);
                this.value = this.colorPalette;
            } else if (currentAction.classList.contains('edit') === true) {
                // set current color item and show the picker element
                this.currentColorItem = currentColor;
                colorPicker.style.display = '';
                colorPicker.setAttribute('value', currentColor.style.backgroundColor);
            } else if (currentAction.classList.contains('delete') === true) {
                // remove the element and set input value again
                currentColor.remove();
                this.value = this.colorPalette;
                // re-render palette content if last item was removed
                if (this.value.length === 0) this.renderPaletteContent();
            }
        });
        this.colorPalette.addEventListener("wheel", (event) => {
            event.preventDefault();
            this.colorPalette.scrollLeft += event.deltaY;
        });
        Sortable.create(this.colorPalette, {
            handle: '.action.move',
            onEnd: () => this.value = this.colorPalette,
        });
    }

    renderPaletteContent() {
        if (this.value.length === 0 && this.mode === 'preview') {
            this.colorPalette.replaceChildren(createElement('slot', {name: 'empty', class: 'new-palette'}));
            return;
        }

        if (this.value.length === 0) {
            const startButton = createElement('button', {class: 'start-button'});
            startButton.appendChild(createElement('slot', {name: 'newButtonIcon'}));
            startButton.appendChild(createElement('slot', {name: 'newButtonText'}));
            startButton.addEventListener('click', (e) => {
                e.preventDefault();
                this._input.value = ['#f49700', '#ff8700', '#75a75a', '#5e8648', '#538bb3', '#426f8f'].join('|');
                this.renderPaletteContent();
            });
            this.colorPalette.replaceChildren(startButton);
            return;
        }

        this.colorPalette.innerHTML = '';
        this.value.forEach((color) => {
            this.colorPalette.appendChild(createColorElement(color, this.mode !== 'preview'));
        });
    }
}

// initialize all spreadsheet data inputs
DocumentService.ready().then(() => {
    customElements.define('color-picker', ColorPickerElement);
    customElements.define('color-palette', ColorPaletteInputElement);
}).catch(() => {
    console.error('Failed to load DOM for processing color palette inputs!');
});
