class Api {
    constructor({ baseUrl, headers }) {
        this._baseUrl = baseUrl;
        this._headers = headers;
    }

    postUrl({ url, short_key }, status) {
        return fetch(this._baseUrl, {
            method: 'POST',
            headers: this._headers,
            body: JSON.stringify({
                url: url,
                short_key: short_key
            })
        })
            .then(res => {
                status.ok = res.ok;
                status.status = res.status;
                return res.json();
            })
    }
}

class Form {
    constructor(formSelector, handleSubmit) {
        this._form = document.querySelector(formSelector);
        this._result = { message: this._form.querySelector('#message'), link: this._form.querySelector('#link') };
        this._btn = this._form.querySelector('.form__submit');
        this._inputs = this._form.querySelectorAll('.form__input');
        this.formValues = {};
        this._handleSubmit = handleSubmit;
    }

    setBtnInactive() { //сделать кнопку неактивной
        this._btn.textContent = 'Секундочку...';
        this._btn.classList.add('form__submit_inactive');
        this._btn.disabled = true;
    }

    setBtnActive() {  //сделать кнопку активной
        this._btn.textContent = 'Сократить';
        this._btn.classList.remove('form__submit_inactive');
        this._btn.disabled = false;
    }

    setResult(message, linkContent = '', linkHref = '') { //вывести результат
        this._result.link.textContent = linkContent;
        this._result.link.href = linkHref;
        this._result.message.textContent = message;
    }

    resetResult() { //очистить результат
        this._result.link.textContent = ' ';
        this._result.link.href = '';
        this._result.message.textContent = '';
    }

    getInputValues() { //вернуть значения инпутов
        this._inputs.forEach((input) => {
            this.formValues[input.name] = input.value;
        });
        return this.formValues;
    }

    addSubmitListner() { //добавить слушателя сабмита
        this._form.addEventListener('submit', this._handleSubmit);
    }
}
