import './styles/app.css';
import './bootstrap';

document.getElementById('show_table').addEventListener('click', function () {
    let date = document.getElementById('date').value;
    let dataRegex = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/;
    let currentDate = new Date().toISOString().slice(0, 10)
    if (!document.getElementById('date').value ) {
        alert('Wybierz date')
    }else if(!dataRegex.test(date) || date > currentDate ){
        alert('Niepoprawny format daty');
    } else {
        fetch('/currency/date', {
            method: 'POST', // or 'PUT'
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(date),
        })
            .then(response => response.json())
            .then(data => {
                let currencyResultFromUser = JSON.parse(data[0]);
                let currencyResultFromToday = JSON.parse(data[1]);
                let table = document.querySelector('table');
                let tbody = table.querySelector('tbody');
                tbody.innerHTML = '';
                for(let rate in currencyResultFromUser.rates ){
                    let percentage = (currencyResultFromToday.rates[rate] - currencyResultFromUser.rates[rate]) / currencyResultFromToday.rates[rate] * 100;
                    let tableBody = `  <tr><td class="currency">${rate}</td>\n` +
                        `                    <td class="today-rate">${currencyResultFromToday.rates[rate]}</td>\n` +
                        `                    <td class="exchange-rate">${currencyResultFromUser.rates[rate]}</td>\n` +
                        `                    <td  class="percentage-difference">${percentage}</td></tr>`
                    tbody.innerHTML += tableBody
                }
                document.querySelector('.course-date').innerText = `Kurs z ${date} `;
                table.style.setProperty('display', 'block', 'important');
            })
            .catch((error) => {
                console.error('Error:', error);
            });

    }
})
