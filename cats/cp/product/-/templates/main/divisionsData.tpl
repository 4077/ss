<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    <div class="divisions">
        <!-- division -->
        <div class="division">
            <div class="info">
                <div class="name">{NAME}</div>
                <div class="price">{PRICE} руб.<!-- division/units -->/{VALUE}<!-- / --></div>
            </div>
            <!-- if division/warehouse -->
            <div class="warehouses">
                <table>
                    <tr>
                        <td></td>
                        <td class="header" width="1">В наличии</td>
                        <td class="header" width="1">В резерве</td>
                        <td class="header" width="1">Доступно</td>
                    </tr>
                    <!-- division/warehouse -->
                    <tr>
                        <td class="header">{NAME}</td>
                        <td>{STOCK}</td>
                        <td>{RESERVED}</td>
                        <td>{AVAILABLE}</td>
                    </tr>
                    <!-- / -->
                    <tr>
                        <td class="total_header">Σ</td>
                        <td class="total stock">{TOTAL_STOCK}</td>
                        <td class="total reserved">{TOTAL_RESERVED}</td>
                        <td class="total available">{TOTAL_AVAILABLE}</td>
                    </tr>
                </table>
            </div>
            <!-- / -->
        </div>
        <!-- / -->
    </div>

</div>
