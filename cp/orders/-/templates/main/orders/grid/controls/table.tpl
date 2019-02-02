<div class="{__NODE_ID__}">

    <div class="items">
        <!-- item -->
        <div class="item">
            <div class="name">{NAME}</div>
            <div class="props">{PROPS}</div>
            <div class="price">{PRICE}</div>
            <div class="quantity">x{QUANTITY}</div>
            <div class="cost">{COST}</div>
            <div class="cb"></div>
        </div>
        <!-- / -->
    </div>

    <!-- delivery -->
    <div class="delivery_cost">
        <div class="cost">{ORDER_TOTAL_COST} руб.</div>
        <div class="label">Сумма заказа</div>
        <div class="cb"></div>

        <!-- if delivery/option -->
        <div class="options">
            <!-- delivery/option -->
            <div class="option">
                <span class="name">{NAME}</span>
                <span class="description">{DESCRIPTION}</span>
                <span class="value">{OPERATOR}{VALUE}</span>
            </div>
            <div class="cb"></div>
            <!-- / -->
        </div>
        <!-- / -->
        <div class="label">Стоимость доставки</div>
        <div class="cost">{COST} руб.</div>
        <div class="cb"></div>
    </div>
    <!-- / -->

    <div class="total_cost">
        <div class="label">Итого</div>
        <div class="cost">{TOTAL_COST} руб.</div>
        <div class="cb"></div>
    </div>

</div>
