//константы
const baseUrl = 'http://localhost:8888/shorter/';
const headers = { 'Content-Type': 'application/json' };

function handleSubmit(evt) { //обработка отправки формы
    evt.preventDefault();
    form.setBtnInactive();
    form.resetResult();
    let status = {};
    api.postUrl(form.getInputValues(), status)
        .then(data => {
            form.setBtnActive();
            if (status.ok) {
                const short_url = baseUrl + data.short_key;
                form.setResult('', short_url, short_url);
            } else throw new Error(data.message);
        })
        .catch(err => {
            console.log(err);
            form.setResult(err.message);
        })
}

//компоненты
const api = new Api({ baseUrl, headers });
const form = new Form('.form', handleSubmit);

//добавляем слушателя
form.addSubmitListner();
