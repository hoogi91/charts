import {createElement, debounce} from "./helper.js";
import css from './colorPicker.css';
import Color from "color";

export class ColorPickerElement extends HTMLElement {

    static get observedAttributes() {
        return ['value'];
    }

    template = `
<div class="picking-area">
    <div class="picker"></div>
</div>
<div class="control-area">
    <div class="hue">
        <div class="slider-picker" style="left: -1px;"></div>
    </div>
    <div class="hue-input">
        <label>H <input type="number" min="0" max="359" name="h"/></label>
        <label>S <input type="number" min="0" max="100" name="s"/></label>
        <label>V <input type="number" min="0" max="100" name="v"/></label>
    </div>
    <div class="alpha">
        <div class="alpha-mask">
            <div class="slider-picker" style="left: calc(100% + 1px);"></div>
        </div>
    </div>
    <div class="rgb-input">
        <label>R <input type="number" min="0" max="255" name="r"/></label>
        <label>G <input type="number" min="0" max="255" name="g"/></label>
        <label>B <input type="number" min="0" max="255" name="b"/></label>
        <label>A <input type="number" min="0" max="1" step="0.01" name="a"/></label>
    </div>
</div>
    `;

    constructor() {
        super();
        this.pickerWrapper = createElement('div', {class: 'picker-wrapper', innerHTML: this.template});

        // build shadow root with css and wrapper
        const style = createElement('style', {textContent: css});
        const backdrop = createElement('div', {class: 'backdrop', append: [this.pickerWrapper]});
        this.attachShadow({mode: 'open'}).append(style, backdrop);

        this.colorArea = this.shadowRoot.querySelector('.picking-area');
        this.colorPicker = this.shadowRoot.querySelector('.picking-area > .picker');
        this.hueArea = this.shadowRoot.querySelector('.hue');
        this.huePicker = this.shadowRoot.querySelector('.hue .slider-picker');
        this.alphaArea = this.shadowRoot.querySelector('.alpha');
        this.alphaPicker = this.shadowRoot.querySelector('.alpha .slider-picker');

        // add events for hue and alpha area
        this.createPickerArea(this.colorArea, (rect, x, y) => {
            const value = 100 - (y * 100 / rect.height);
            const saturation = x * 100 / rect.width;
            this.color = this.color.saturationv(saturation).value(value);
        });
        this.createPickerArea(this.hueArea, (rect, x) => {
            this.color = this.color.hue((359 * x) / rect.width); // if (hue === 360) hue = 359;
        });
        this.createPickerArea(this.alphaArea, (rect, x) => {
            this.color = this.color.alpha((x / rect.width).toFixed(2));
        });

        // catch all input changes and adjust values
        this.shadowRoot.querySelectorAll('input').forEach((input) => {
            input.addEventListener('change', (event) => {
                const type = input.getAttribute('name');
                const parsedValue = parseInt(event.target.value);

                // update existing value by input and set again
                let color = this.color;
                if (type === 'h') color = color.hue(parsedValue);
                else if (type === 's') color = color.saturationv(parsedValue);
                else if (type === 'v') color = color.value(parsedValue);
                else if (type === 'r') color = color.red(parsedValue);
                else if (type === 'g') color = color.green(parsedValue);
                else if (type === 'b') color = color.blue(parsedValue);
                else if (type === 'a') color = color.alpha(parseFloat(event.target.value).toFixed(2));
                this.color = color;
            });
        });

        // add close on backdrop click
        backdrop.addEventListener('click', (event) => {
            // go away if user is moving a picker
            if (this.isMoving === true) return;

            // if clicking outside close picker and dispatch event
            if (this.pickerWrapper.contains(event.target) === false) {
                this.style.display = 'none';
                this.triggerColorChanged();
            }
        });
    }

    connectedCallback() {
        // temporary fix for firefox => https://bugzilla.mozilla.org/show_bug.cgi?id=1502814
        this.__proto__ = customElements.get('color-picker').prototype;
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'value') {
            this.color = Color(newValue);
        }
    }

    get color() {
        return this._value || Color('#fff');
    }

    set color(newColor) {
        this._value = newColor;

        // update all input values
        this.shadowRoot.querySelectorAll('input').forEach((input) => {
            const type = input.getAttribute('name');
            if (type === 'h') input.value = parseInt(newColor.hue(), 10).toString();
            else if (type === 's') input.value = parseInt(newColor.saturationv(), 10).toString();
            else if (type === 'v') input.value = parseInt(newColor.value(), 10).toString();
            else if (type === 'r') input.value = parseInt(newColor.red(), 10).toString();
            else if (type === 'g') input.value = parseInt(newColor.green(), 10).toString();
            else if (type === 'b') input.value = parseInt(newColor.blue(), 10).toString();
            else if (type === 'a') input.value = parseFloat(newColor.alpha()).toFixed(2).toString();
        });

        // update picker and alpha colors
        this.shadowRoot.host.style.setProperty('--color-area-bg-color', Color(newColor).saturationv(100).value(100).rgb());
        this.shadowRoot.host.style.setProperty('--alpha-bg-color', newColor.rgb());

        // update all picker positions;
        const halfPickerWidth = this.colorPicker.getBoundingClientRect().width / 2;
        this.colorPicker.style.top = ((100 - newColor.value()) / 100) * this.colorArea.getBoundingClientRect().height - halfPickerWidth + 'px';
        this.colorPicker.style.left = (newColor.saturationv() / 100) * this.colorArea.getBoundingClientRect().width - halfPickerWidth + 'px';
        this.huePicker.style.left = Math.max((newColor.hue() / 359) * this.hueArea.getBoundingClientRect().width - 2, -1) + 'px';
        this.alphaPicker.style.left = Math.max(newColor.alpha() * this.alphaArea.getBoundingClientRect().width - 2, -1) + 'px';

        // always dispatch color changed event
        this.triggerColorChanged();
    }

    createPickerArea(area, callback) {
        this.setMouseTracking(area, (e) => {
            let rect = area.getBoundingClientRect();
            let x = e.clientX - rect.left;
            let y = e.clientY - rect.top;
            if (x > rect.width) x = rect.width;
            if (y > rect.width) y = rect.height;
            if (x < 0) x = 0;
            if (y < 0) y = 0;
            callback(rect, x, y);
        });
    }

    setMouseTracking(element, callback) {
        // start event listening in element itself
        element.addEventListener('mousedown', (e) => {
            this.isMoving = true;
            callback(e);
            this.shadowRoot.addEventListener('mousemove', callback);
        });

        // stop listening when mouse is up in color-picker area
        this.pickerWrapper.addEventListener('mouseup', () => {
            this.shadowRoot.removeEventListener('mousemove', callback);
            this.isMoving = false;
        });
    }

    get triggerColorChanged() {
        if (typeof this._colorChangedDispatcher === 'undefined') {
            this._colorChangedDispatcher = debounce(250, () => {
                this.dispatchEvent(new CustomEvent("colorChanged", {detail: {color: this.color}}));
            });
        }

        return this._colorChangedDispatcher;
    }
}
