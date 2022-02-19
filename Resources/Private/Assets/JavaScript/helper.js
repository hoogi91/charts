export function createElement(tagName, attributes = {}) {
    const element = document.createElement(tagName);
    Object.entries(attributes).forEach(entry => {
        let [name, value] = entry;
        if (name === 'textContent') {
            element.textContent = value;
        } else if (name === 'innerHTML') {
            element.innerHTML = value;
        } else if (name === 'append') {
            if (value instanceof NodeList) {
                const childCopy = [];
                value.forEach(function (item) {
                    childCopy.push(item.cloneNode(true));
                });
                value = childCopy;
            }

            element.append(...value);
        } else {
            element.setAttribute(name, value);
        }
    });
    return element;
}

export function createColorElement(color, showActions = false, attributes = {}) {
    attributes.class = 'color' + (isLightColor(color) ? ' light' : '');
    attributes.style = 'background-color:' + color;
    const colorElement = createElement('div', attributes);

    if (showActions === true) {
        colorElement.innerHTML = `
            <span class="action edit">
                ${getHexValue(color)}
            </span>
            <span class="action move">
                <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><g class="icon-color" transform="matrix(1.05, 0, 0, 1.05, -0.5, -0.5)"><path d="M14.823 7.823l-2.396-2.396a.25.25 0 0 0-.427.177V7H9V4h1.396a.25.25 0 0 0 .177-.427L8.177 1.177a.25.25 0 0 0-.354 0L5.427 3.573A.25.25 0 0 0 5.604 4H7v3H4V5.604a.25.25 0 0 0-.427-.177L1.177 7.823a.25.25 0 0 0 0 .354l2.396 2.396A.25.25 0 0 0 4 10.396V9h3v3H5.604a.25.25 0 0 0-.177.427l2.396 2.396a.25.25 0 0 0 .354 0l2.396-2.396a.25.25 0 0 0-.177-.427H9V9h3v1.396a.25.25 0 0 0 .427.177l2.396-2.396a.25.25 0 0 0 0-.354z"></path></g></svg>
            </span>
            <span class="action delete">
                <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><g class="icon-color" transform="matrix(1.6, 0, 0, 1.6, -4.9, -4.9)"><path d="M11.9 5.5L9.4 8l2.5 2.5c.2.2.2.5 0 .7l-.7.7c-.2.2-.5.2-.7 0L8 9.4l-2.5 2.5c-.2.2-.5.2-.7 0l-.7-.7c-.2-.2-.2-.5 0-.7L6.6 8 4.1 5.5c-.2-.2-.2-.5 0-.7l.7-.7c.2-.2.5-.2.7 0L8 6.6l2.5-2.5c.2-.2.5-.2.7 0l.7.7c.2.2.2.5 0 .7z"></path></g></svg>
            </span>
            <span class="action add">
                <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><g class="icon-color"><path d="M12.5 9H9v3.5c0 .3-.2.5-.5.5h-1c-.3 0-.5-.2-.5-.5V9H3.5c-.3 0-.5-.2-.5-.5v-1c0-.3.2-.5.5-.5H7V3.5c0-.3.2-.5.5-.5h1c.3 0 .5.2.5.5V7h3.5c.3 0 .5.2.5.5v1c0 .3-.2.5-.5.5z"/></g></svg>
            </span>
        `;
    }
    return colorElement;
}

export function getHexValue(color) {
    return color.alpha() < 1 ? color.hexa() : color.hex();
}

export function isLightColor(color) {
    return color.luminosity() > 0.6;
}

export function debounce(wait, func, immediate) {
    let timeout;
    return function () {
        const context = this, args = arguments;
        const later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}
