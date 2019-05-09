<div class="{__NODE_ID__}" instance="{__INSTANCE__}">
    <div class="update_button">
        <div class="idle">
            <div class="icon fa fa-angle-double-right"></div>
        </div>
        <div class="proc">
            <div class="progress">
                <div class="bar"></div>
                <div class="info">
                    <span class="status"></span>
                    <span class="position"></span>
                    <span class="percent"></span>
                </div>

                <div class="break_button">прервать</div>
            </div>
        </div>
    </div>

    <div class="endpoints">
        <div class="row trees">
            <div class="cell source">
                {SOURCE_TREE_SETTINGS}
            </div>

            <div class="cell center"></div>

            <div class="cell target">
                {TARGET_TREE_SETTINGS}
            </div>
        </div>

        <div class="row fields">
            <div class="cell source">
                {SOURCE_FIELD_SETTINGS}
            </div>

            <div class="cell collation_mode">
                {COLLATION_MODE_SELECTOR}
            </div>

            <div class="cell target">
                {TARGET_FIELD_SETTINGS}
            </div>
        </div>

    </div>

    <div class="collation_cp">
        <div class="connections_count">
            {CONNECTIONS_COUNT}
        </div>
        <div class="collation_button">
            <div class="idle">
                <div class="icon fa fa-refresh"></div>
            </div>
            <div class="proc">
                <div class="progress">
                    <div class="bar"></div>
                    <div class="info">
                        <span class="position"></span>
                        <span class="percent"></span>
                    </div>
                </div>

                <div class="break_button">прервать</div>
            </div>
        </div>

        {*{COLLATE_TEST_BUTTON}*}
        {*{COLLATE_TEST_PROC_BUTTON}*}
    </div>

    <!-- update_cp -->
    <div class="update_cp">
        <div class="bar">
            <div class="tabs">
                <!-- update_cp/tab -->
                {BUTTON}
                <!-- / -->
            </div>

            <div class="posthandler {POSTHANDLER_ENABLED_CLASS}">
                {POSTHANDLER_TOGGLE_BUTTON}
                {POSTHANDLER_EDIT_BUTTON}
            </div>
        </div>

        <div class="content {CLASS}">
            {CONTENT}
        </div>
    </div>
    <!-- / -->

</div>
