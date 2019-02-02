<div class="{__NODE_ID__}" instance="{__INSTANCE__}" product_id="{PRODUCT_ID}">

    <div class="row branch">
        <div class="tree_name {TREE_CLASS}">
            <div class="icon {TREE_ICON}"></div>
            <div class="label">{TREE_NAME}</div>
        </div>

        <div class="cat_branch" title="{BRANCH_TITLE}">
            <!-- branch_node -->
            <div class="node" cat_id="{ID}">
                <div class="icon fa fa-chevron-right"></div>
                <div class="name">{NAME}</div>
            </div>
            <!-- / -->
        </div>
    </div>

    <div class="row cp">
        <div class="left">
            <!-- moderation -->
            <div class="status_selector {STATUS_CLASS}">
                <div class="button">
                    <div class="icon {ICON_CLASS}"></div>
                </div>
                <div class="dropdown">
                    <!-- moderation/status -->
                    {BUTTON}
                    <!-- / -->
                </div>
            </div>
            <!-- / -->

            <div class="enable_modes">
                {ENABLED_BUTTON}
                {PUBLISHED_BUTTON}
            </div>
        </div>

        <div class="right">
            <div class="status_cp">
                <!-- status -->
                <div class="status {CLASS}">
                    <div class="icon"></div>
                    <div class="label"></div>
                </div>
                <!-- / -->

                {SEND_TO_MODERATION_BUTTON}
            </div>
        </div>
    </div>

    <div class="row info">
        <table>
            <!-- info_field -->
            <tr>
                <td class="label" width="1">{LABEL}</td>
                <td class="value">{VALUE}</td>
            </tr>
            <!-- / -->
        </table>
    </div>

    <div class="row">
        <div class="field name">
            <div class="label">Наименование</div>
            <div class="control">
                <input type="text" class="product_field" field="name" value="{NAME}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="settings left">
            <div class="row">
                <div class="field units">
                    <div class="label">Ед. изм.</div>
                    <div class="control">
                        <input type="text" class="product_field" field="units" value="{UNITS}">
                    </div>
                </div>

                <div class="field unit_size">
                    <div class="label">Кратность</div>
                    <div class="control">
                        <input type="text" class="product_field" field="unit_size" value="{UNIT_SIZE}">
                    </div>
                </div>

                <div class="field alt_units">
                    <div class="label">Доп. ед. изм.</div>
                    <div class="control">
                        <input type="text" class="product_field" field="alt_units" value="{ALT_UNITS}">
                    </div>
                </div>
            </div>

            <div class="row">
                {DIVISIONS_DATA}
            </div>
        </div>

        <div class="settings right">
            <div class="row images">
                <div class="label">Картинки <a class="google image_link" href="{GOOGLE_LINK}" target="_blank">Google</a> <a class="yandex image_link" href="{YANDEX_LINK}" target="_blank">Яндекс</a></div>
                <!-- stock_photo_request -->
                <div class="stock_photo_request">
                    {CONTENT}
                </div>
                <!-- / -->
                <div class="control">
                    <div class="images">
                        {IMAGES}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="label">Характеристики</div>
                <div class="control">{PROPS}</div>
            </div>
        </div>
    </div>

</div>