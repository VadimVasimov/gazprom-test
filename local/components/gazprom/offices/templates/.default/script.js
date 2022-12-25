document.addEventListener('DOMContentLoaded', () => {
    ymaps.ready(function () {
        let myMap = new ymaps.Map('offices-map', {
            center: [59.950272431485, 30.316633829704],
            zoom: 10,
            controls: ['smallMapDefaultSet', 'mediumMapDefaultSet', 'largeMapDefaultSet']
        }, {
            searchControlProvider: 'yandex#search'
        });

        // добавляем метки на карту
        for (let i = 0; i < offices.length; i++) {
            let office = offices[i];

            let placemark = new ymaps.Placemark(office.COORDINATES.split(','), {
                balloonContentHeader:
                    '<span>'+ office.NAME +'</span>',
                balloonContentBody:
                    '<span><b>Город:</b> '+ office.CITY +'</span><br/>' +
                    '<span><b>Телефон:</b> '+ office.PHONE +'</span><br/>' +
                    '<span><b>Email:</b> '+ office.EMAIL +'</span><br/>',
                hintContent: office.NAME
            });

            myMap.geoObjects.add(placemark);
        }
    });
});