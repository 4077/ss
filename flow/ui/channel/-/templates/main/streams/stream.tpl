<div class="{__NODE_ID__} {ENABLED_CLASS}" instance="{__INSTANCE__}">

    <div class="cp">
        {TOGGLE_BUTTON}
        <div class="selector">
            {SOURCE_DIVISION_SELECTOR}
        </div>
        <div class="selector">
            {TARGET_DIVISION_SELECTOR}
        </div>
        {DELETE_BUTTON}
    </div>

    <!-- data -->
    <div class="data">
        <div class="price field {PRICE_ENABLED_CLASS}">
            {TOGGLE_PRICE_BUTTON}
            <div class="use_coefficients_table">
                {PRICE_USE_INTERSECTIONS_TABLE_SELECTOR}
            </div>
            <div class="coefficient">
                <!-- data/price_coefficients_table_value -->
                <div class="table_value {DOES_NOT_HAVE_CLASS}">
                    {VALUE}
                </div>
                <!-- / -->
                <!-- data/price_coefficient_manual_value -->
                <div class="manual_value">
                    {TXT}
                </div>
                <!-- / -->
            </div>
        </div>
        <div class="discount field {DISCOUNT_ENABLED_CLASS}">
            {TOGGLE_DISCOUNT_BUTTON}
        </div>
    </div>

    <div class="warehouses">
        <!-- warehouse -->
        <div class="warehouse {ENABLED_CLASS}">
            <div class="cp">
                {TOGGLE_BUTTON}
                <div class="selector">
                    {SELECTOR}
                </div>
                <!-- warehouse/data -->
                <div class="data">
                    <div class="stock field {STOCK_ENABLED_CLASS}">
                        {TOGGLE_STOCK_BUTTON}
                    </div>
                    <div class="reserved field {RESERVED_ENABLED_CLASS}">
                        {TOGGLE_RESERVED_BUTTON}
                    </div>
                </div>
                <!-- / -->
                {DELETE_BUTTON}
            </div>

        </div>
        <!-- / -->
        {CREATE_WAREHOUSES_STREAM_BUTTON}
    </div>
    <!-- / -->

</div>
