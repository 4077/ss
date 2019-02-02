<div class="{__NODE_ID__} focusable {FOCUS_CLASS}" instance="{__INSTANCE__}">

    <div class="cp">
        <div class="left">
            <div class="filter">
                <div class="status">
                    <!-- status_filter -->
                    {BUTTON}
                    <!-- / -->
                </div>
                <div class="user">
                    {FILTER_USER_SELECT}
                </div>
            </div>

            <div class="sep"></div>

            <div class="add">
                <div class="add button">
                    <div class="icon fa fa-plus"></div>
                </div>
                <div class="user_select">
                    {ADD_USER_SELECT}
                </div>
            </div>

            {NOTIFY_DIALOG_BUTTON}
        </div>

        <div class="right">
            <div class="paginator">
                {PAGINATOR}
            </div>
        </div>
    </div>

    <div class="content">
        <table>
            <!-- request -->
            <tr class="request row {STATUS_CLASS}" n="{N}" request_id="{ID}" product_id="{PRODUCT_ID}">
                <td class="icon {STATUS_CLASS}" title="{STATUS_TITLE}">
                    <div class="fa {ICON_CLASS}"></div>
                </td>
                <td class="date {STATUS_CLASS}" width="1">{DATE}</td>
                <td class="name">{NAME}</td>
                <td class="user" width="1">{USER}</td>
            </tr>
            <!-- / -->
        </table>
    </div>

</div>
