<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    <table>
        <!-- if division_row -->
        <tr>
            <td></td>
            <td>Цена</td>
        </tr>
        <!-- division_row -->
        <tr>
            <td>{DATETIME}</td>
            <td>{PRICE}</td>
        </tr>
        <!-- / -->
        <!-- / -->

        <!-- if warehouse_row -->
        <tr>
            <td></td>
            <td>Наличие</td>
            <td>Резерв</td>
        </tr>
        <!-- warehouse_row -->
        <tr>
            <td>{DATETIME}</td>
            <td>{STOCK}</td>
            <td>{RESERVED}</td>
        </tr>
        <!-- / -->
        <!-- / -->
    </table>

</div>
