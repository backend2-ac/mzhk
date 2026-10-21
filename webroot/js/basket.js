//Скрытая таблица для отправки формы
const textarea = document.querySelector('.textarea-table');
const textareaID = document.querySelector('.textarea-table_2')

function requestTable() {
    const cart = JSON.parse(localStorage.getItem('DosOrtapediaCart')) || {};
    let totalSumma = document.querySelector('.js-all-summa').innerHTML
    const ids = Object.keys(cart);

    textareaID.innerHTML = localStorage.getItem('DosOrtapediaCart');

    let tableItem = '';
    // let tableItemID = '';
    // let totalSumma = 0;
    let tableTotalPrice = '';
    const renderTable = () => {
        for (let i in ids) {
            const keys = ids[i];
            const id = cart[keys].id
            const name = cart[keys].name;
            const size = cart[keys].size;
            const quantity = cart[keys].quantity;
            const totalprice = cart[keys].totalprice;
            tableItem += `
                <tr>
                <td>${id}</td>
                <td>${name}</td>
                
                <td>${quantity}</td>
                <td>${totalprice} тг</td>
                </tr>
            `
                // tableItemID += `${id}, `;
                // totalSumma += totalprice;
            tableTotalPrice = `
            <tr>
                <td colspan="6">Итоговая сумма всех товаров - ${totalSumma.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")} </td>
            </tr>
            `;
        }
        textarea.innerHTML = `
        <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
            <th>ID Товара</th>
            <th>Название</th>
           
            <th>Количество</th>
            <th>Итоговая сумма</th>
            </tr>
        </thead>
        <tbody>${tableItem} ${tableTotalPrice}</tbody>
        </table>
        `;
        // textareaID.innerHTML = `${tableItemID}`;
    }
    renderTable();
}

if (textarea) {
    requestTable();
}



//Корзина магазина
function requestCart() {

    const cartDOMElement = document.querySelector('.js-cart')
        // const cartCompareDOMElement = document.querySelector('.compare__slider')
    const cartItemsCounterDOMElement = document.querySelectorAll('.js-cart-total-count-items')
    const cartTotalPriceDOMElement = document.querySelectorAll('.js-cart-total-summa')
    const cartTotalSummaDOMElement = document.querySelector('.js-cart-total-price') ///////////////////////
        // const totalSumma = document.querySelector('.js-all-summa')

    const cart = JSON.parse(localStorage.getItem('DosOrtapediaCart')) || {};
    // const cartCompare = JSON.parse(localStorage.getItem('DosOrtapediaCartCompare')) || {};


    const clearBusket = () => {
        const table = document.querySelector('.shoppingcard')
            // const total = document.querySelector('.count')
            // const form = document.querySelector('.feedback__container')
        const empty = document.querySelector('.empty')
        if (Object.keys(cart).length == 0) {
            table.classList.add('disabled');
            // total.classList.add('disabled');
            // form.classList.add('disabled');
            empty.classList.add('active');
        }
    }
    const busketpage = document.querySelector('.shoppingcard')
    if (busketpage) {
        clearBusket();
    }
    //отображаем добавленный товар в корзине
    const renderCartItem = ({ id, articul, name, totalprice, price, src, quantity, href }) => {
        const cartItemDOMElement = document.createElement('div');
        if (articul === null) {
            articul = '';
        }
        // totalprice = totalprice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        price = price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
        const cartItemTemplate =
            `
                <a href="${href}" class="shoppingcard__item-img">
                    <img src="${src}" alt="">
                </a>
                <div class="shoppingcard__item-info">
                    <a href="${href}" class="shoppingcard__item-title">${name}</a>


                         <div class="shoppingcard__item-amount ">
                                    <p class="quantity-title">Количество</p>

                                    <button class="minus js-minus">-</button>

                                    <p class="quantity shoppingcard__nums js-cart-item-quantity">${quantity}</p>
                                    <button class="plus js-plus">+</button>

                                </div>
                    <p class="shoppingcard__item-price  js-cart-total-summa">${price}  тг</p>
                    <span class="js-cart-item-totalprice">${totalprice} тг</span>
                </div>

                <div class="shoppingcard__item-delete busket__card-remove remove">Удалить</div>
                `

        ;

        cartItemDOMElement.innerHTML = cartItemTemplate;
        cartItemDOMElement.setAttribute('data-id', id);
        cartItemDOMElement.classList.add('shoppingcard__item');
        cartDOMElement.appendChild(cartItemDOMElement);
        totalBusket();
        updateCart();
    }


    //сохраняем товар в LocalStorage
    const saveCart = () => {
        localStorage.setItem('DosOrtapediaCart', JSON.stringify(cart));
    }


    // подсчитываение колличества и суммы товара
    const totalBusket = () => {
        let totalcount = 0;
        const ids = Object.keys(cart);
        for (let i = 0; i < ids.length; i++) {
            const id = ids[i]
            totalcount += +(cart[id].quantity);
        }

        let totalAll = 0;
        const price = document.querySelectorAll('.js-cart-item-totalprice');
        for (let i = 0; i < price.length; i++) {
            totalAll = totalAll + parseInt(price[i].innerHTML);
        }

        cartTotalPriceDOMElement.textContent = totalAll.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' тг';
        // cartTotalSummaDOMElement.textContent = total + ' тг';
        cartItemsCounterDOMElement.forEach(elem => {
            // elem.textContent = totalcount;
        })
        cartTotalPriceDOMElement.forEach(elem => {
            elem.textContent = totalAll.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' тг'
        })
        $('.js-cart-total-summa').attr('data-summ', totalAll);

        if (ids.length == 0) {
            cartTotalPriceDOMElement.forEach(elem => {
                    elem.textContent = totalAll + ' тг'
                })
                // cartTotalSummaDOMElement.textContent = 0;
            $('.js-cart-total-summa').attr('data-summ', 0);
        }
        updateCart();
        // checkSelectDeliv();
    }

    function totalBusketHeader() {
        let busket = document.querySelector('.count__busket')
        let totalcount = 0;
        const ids = Object.keys(cart);
        for (let i = 0; i < ids.length; i++) {
            const id = ids[i]
            totalcount += +(cart[id].quantity);
        }
        console.log(totalcount + "yhuvghvghvgfvcf")
            // busket.innerHTML = totalcount;
        if (totalcount > 0) {
            busket.classList.add('active')
        } else {
            busket.classList.remove('active')
        }
    }
    totalBusketHeader();
    //Проверка выбранного селекта для доставки товара
    let select = document.getElementById('deliv')
    if (select) {
        select.addEventListener('input', checkSelectDeliv)
    }

    function checkSelectDeliv() {
        let summa = document.querySelector('.js-all-summa')
        let deliv = document.querySelector('.deliv')
        let pickup = document.querySelector('.pickup')
        let potomdelete = document.querySelector('potomdelete')

        let select = document.getElementById('deliv')
        let value = select.value
        let totalAll = 0;
        // let price = document.querySelectorAll('.js-cart-item-totalprice');
        // for (let i = 0; i < price.length; i++) {
        //     let parseSumma = totalAll + parseInt(price[i].innerHTML)
        //     totalAll = parseSumma;
        // }
        if (value === 'delivery') {
            // let parseSumma = parseInt(totalAll + 2000)
            // summa.innerHTML = parseSumma.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' тг';
            deliv.classList.add('active')
            pickup.classList.remove('active')

        } else {
            // summa.innerHTML = totalAll.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' тг'
            deliv.classList.remove('active')
            pickup.classList.add('active')

        }
        requestTable();
        // console.log(value)

        if (value === 'pickup') {
            // let parseSumma = parseInt(totalAll + 2000)
            // summa.innerHTML = parseSumma.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' тг';
            pickup.classList.add('active')

        } else {
            // summa.innerHTML = totalAll.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' тг'
            pickup.classList.remove('active')

        }


    }
    //Удаление из корзины
    const deleteCartItem = (id) => {
            const cartItemDOMElement = cartDOMElement.querySelector(`[data-id="${id}"]`);
            // const tableElement = tableDOMElement.querySelector(`[data-product-articul="${articul}"]`);
            cartDOMElement.removeChild(cartItemDOMElement);
            // tableDOMElement.removeChild(tableElement);
            delete cart[id];
            updateCart();
            totalBusket();
        }
        //Обновление количества товара и итоговой суммы
    const updateQuantityTotalPrice = (id, quantity) => {
            const cartItemDOMElement = cartDOMElement.querySelector(`[data-id="${id}"`);
            const cartItemQuantityDOMElement = cartItemDOMElement.querySelector('.js-cart-item-quantity');
            // const cartTotalPriceDOMElement = document.querySelector('.js-cart-total-price')
            const cartItemPriceDOMElement = cartItemDOMElement.querySelector('.js-cart-item-totalprice');

            const ids = Object.keys(cart);
            cart[id].quantity = quantity;
            cartItemQuantityDOMElement.textContent = quantity;
            cart[id].totalprice = cart[id].quantity * cart[id].price;
            cartItemPriceDOMElement.textContent = cart[id].totalprice + ' тг';
            console.log(cart[id].totalprice)

            // tableQuantity.textContent = quantity;
            cart[id].totalprice = cart[id].quantity * cart[id].price;
            // tableTotal.textContent = cart[articul].totalprice;

            updateCart();
        }
        //Увеличение количества товара и итоговой суммы
    const increaseQuantity = (id) => {
            const newQuantity = cart[id].quantity + 1;
            updateQuantityTotalPrice(id, newQuantity);
        }
        //Уменьшение количества товара и итоговой суммы
    const decreaseQuantity = (id) => {
        const newQuantity = cart[id].quantity - 1;
        if (newQuantity >= 1) {
            updateQuantityTotalPrice(id, newQuantity);
        }
    }



    //Добавление в корзину
    const addCartItem = (data) => {
        // console.log(data)
        const { id } = data;
        cart[id] = data;
        updateCart();
        if (cartDOMElement) {
            renderCartItem(data);
        }
    }


    //Обновляем данные в LocalStorage
    const updateCart = () => {
        saveCart();
    }


    //Получаем данные для объекта
    const getProductData = (productDOMElement) => {
        const button = document.querySelector('.buy__product')
        const id = productDOMElement.getAttribute('data-id')
        const name = productDOMElement.getAttribute('data-product-name');
        // const desc = productDOMElement.getAttribute('data-product-desc');
        const articul = productDOMElement.getAttribute('data-product-articul');
        const size = productDOMElement.getAttribute('data-product-size');
        // const color = productDOMElement.getAttribute('data-product-color');
        const price = productDOMElement.getAttribute('data-product-price');
        const src = productDOMElement.getAttribute('data-product-img');
        let href = productDOMElement.getAttribute('data-product-href');

        const quantity = 1;
        const totalprice = quantity * +(price);
        return { id, name, articul, size, price, totalprice, src, quantity, href };
    }



    const renderCart = () => {
        const ids = Object.keys(cart);
        // console.log(ids);
        ids.forEach((id) => renderCartItem(cart[id]));
    };


    const disabledButton = () => {
        // console.log(cart)
        const test = document.querySelectorAll('.js-product')
        const button = document.querySelector('.buy__product')
        for (let i = 0; i < test.length; i++) {
            const attr = (test[i].getAttribute('data-id'))
            const parent = test[i].querySelector('.js-buy')
                // console.log(parent)
                // console.log(cart.hasOwnProperty(attr))
            if (cart.hasOwnProperty(attr)) {

                /** */
                // let productnotinbasket = document.querySelector(".productnotinbasket")
                // let productinbasket = document.querySelector(".productinbasket")

                /** */
                parent.classList.add('disabled')
                    // parent.innerHTML = 'Товар в корзине'
                parent.disabled = true;
                if (button) {
                    // button.innerHTML = 'Товар в корзине'
                    // document.getElementsByClassName("productinbasket").style.display = "block";
                }
            }

        }

    }
    disabledButton();


    // Вызов popup
    function showPopup() {
        let popup = document.querySelector('.popup-busket')
        let body = document.querySelector('body')
        let btn = document.querySelector('.popup-busket__link.js')
        popup.classList.remove('hidden')
        body.style.overflow = 'hidden'
        setTimeout(() => { popup.classList.add('active') }, 50)
        btn.addEventListener('click', closePopup)
        popup, addEventListener('click', (e) => {
            if (e.target == popup) {
                closePopup();
            }
        })

        function closePopup() {
            popup.classList.remove('active')
            body.style.overflow = 'unset'
            setTimeout(() => { popup.classList.add('hidden') }, 300)
        }
    }


    //Инициализация
    const cartInit = () => {
        if (cartDOMElement) {
            renderCart();
        }

        document.querySelector('body').addEventListener('click', (e) => {
            const target = e.target;
            //В корзину
            if (target.classList.contains('js-buy')) {
                e.preventDefault();
                const productDOMElement = target.closest('.js-product');
                const data = getProductData(productDOMElement);
                addCartItem(data);
                disabledButton();
                totalBusketHeader();
                showPopup();
            }

            //Удалить из корзины
            if (target.classList.contains('remove')) {
                e.preventDefault();
                const cartItemDOMElement = target.closest('.shoppingcard__item');
                const productId = cartItemDOMElement.getAttribute('data-id');
                deleteCartItem(productId);
                clearBusket();
                requestTable();
                totalBusketHeader();
            }

            //Увеличить
            if (target.classList.contains('js-plus')) {
                e.preventDefault();
                const cartItemDOMElement = target.closest('.shoppingcard__item');
                const productId = cartItemDOMElement.getAttribute('data-id');
                increaseQuantity(productId);
                totalBusket();
                requestTable();
                totalBusketHeader();
            }

            //Уменьшить
            if (target.classList.contains('js-minus')) {
                e.preventDefault();
                const cartItemDOMElement = target.closest('.shoppingcard__item');
                const productId = cartItemDOMElement.getAttribute('data-id');
                decreaseQuantity(productId);
                totalBusket();
                requestTable();
                totalBusketHeader();
            }


        });
    }
    cartInit();
}
requestCart();

// function requestCart() {
//     const cartDOMElement = document.querySelector('.js-cart')
//     const cart = JSON.parse(localStorage.getItem('cart')) || {};

//     const renderCartItem = ({ articul, name, desc, color, size, price, totalprice, src, quantity }) => {
//         const cartItemDOMElement = document.createElement('div');

//         const cartItemTemplate = `
//     <tr class="product__item">
//       <td class="product__title">
//         <a href="#"><img src="${src}"></a>
//       </td>
//       <td class="product__name">
//         <h2>${name}</h2>
//         <p>${desc}</p>
//       </td>
//       <td class="product__size-color">
//         <p>${size}</p>
//       </td>
//       <td class="product__count">
//         <p>${quantity}</p>
//       </td>
//       <td class="product__price">
//         <p>${price}</p>
//       </td>
//       <td class="product__summ">
//         <p>${totalprice}</p>
//       </td>
//       <td class="product__remove">
//         <button class="remove buy"><img src="images/remove.svg" alt=""></button>
//       </td>
//     </tr>









//     <div class="shoppingcard__item">
//         <div class="shoppingcard__item-img">
//         <img src="${src}" alt="">
//         </div>
//         <div class="shoppingcard__item-info">
//             <p class="shoppingcard__item-title">${name}</p>
//             <div class="shoppingcard__item-amount">
//             <div class="shoppingcard__bottom-item count__busket">
//             </div>
//         </div>
//         <p class="shoppingcard__item-price  js-cart-total-summa">${price}</p>
//         </div>

//     <div class="shoppingcard__item-delete">Удалить</div>
//     </div>
//   `;
//         cartItemDOMElement.innerHTML = cartItemTemplate;
//         cartItemDOMElement.setAttribute('data-product-articul', articul)
//         cartItemDOMElement.classList.add('js-cart-item')
//         cartDOMElement.appendChild(cartItemDOMElement);
//     }

//     const saveCart = () => {
//         localStorage.setItem('cart', JSON.stringify(cart));
//     }

//     const updateCart = () => {
//         console.log(cart);
//         saveCart();
//     }

//     const deleteCartItem = (articul) => {
//         const cartItemDOMElement = cartDOMElement.querySelector(`[data-product-articul="${articul}"]`);
//         cartDOMElement.removeChild(cartItemDOMElement);
//         delete cart[articul];
//         updateCart();
//         console.log(cartItemDOMElement)
//     }

//     const addCartItem = (data) => {
//         const { articul } = data;
//         cart[articul] = data;
//         updateCart();
//         renderCartItem(data);
//     }


//     const getProductData = (productDOMElement) => {
//         const name = productDOMElement.getAttribute('data-product-name');
//         const desc = productDOMElement.getAttribute('data-product-desc');
//         const articul = productDOMElement.getAttribute('data-product-articul');
//         const size = productDOMElement.getAttribute('data-product-size');
//         const color = productDOMElement.getAttribute('data-product-color');
//         const price = productDOMElement.getAttribute('data-product-price');
//         const src = productDOMElement.getAttribute('data-product-img');
//         const quantity = 1;
//         const totalprice = quantity * +(price);
//         return { name, desc, articul, size, color, price, totalprice, src, quantity, };
//     }

//     const renderCart = () => {
//         const ids = Object.keys(cart);
//         // console.log(ids);
//         ids.forEach((articul) => renderCartItem(cart[articul]));
//     };

//     const cartInit = () => {
//         if (cartDOMElement) {
//             renderCart();
//         }

//         document.querySelector('body').addEventListener('click', (e) => {
//             const target = e.target;
//             if (target.classList.contains('js-buy')) {
//                 e.preventDefault();
//                 const productDOMElement = target.closest('.js-product');
//                 const data = getProductData(productDOMElement);
//                 addCartItem(data);
//             }
//             if (target.classList.contains('remove')) {
//                 e.preventDefault();
//                 const cartItemDOMElement = target.closest('.js-cart-item');
//                 const productArticul = cartItemDOMElement.getAttribute('data-product-articul');
//                 deleteCartItem(productArticul);
//             }
//         });
//     }

//     cartInit();
// }

// requestCart();

// let alertt = document.querySelector(".alert--fixed");
// let alertClose = document.querySelectorAll(".alert--close")
// for (let item of alertClose) {
//     item.addEventListener('click', function(event) {
//         alertt.classList.remove("alert--active")
//         alertt.classList.remove("alert--warning")
//         alertt.classList.remove("alert--error")
//     })
// }

function submitForm() {
    $('#form_loader').show();
    const busket = document.querySelector('.shoppingcard')
    if (busket) {
        clearLocalStorage();
    }
    console.log('123')
}

function clearLocalStorage() {
    localStorage.removeItem('DosOrtapediaCart');
}