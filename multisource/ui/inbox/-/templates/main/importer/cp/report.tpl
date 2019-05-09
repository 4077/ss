<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    <!-- importer -->
    <div class="importer {MATCHED_CLASS}" importer_id="{ID}" pivot_id="{PIVOT_ID}">
        <div class="name">{NAME}</div>
        <div class="break_button">
            <div class="icon fa fa-close"></div>
        </div>
        {IMPORT_BUTTON}
        <div class="cells">
            <!-- importer/cell -->
            <div class="cell {MATCH_CLASS}" title="{TITLE}">{COORD}</div>
            <!-- / -->
        </div>
        <div class="bottom_bar">
            <div class="sheets">
                <!-- importer/sheet -->
                <div class="sheet {DETECTED_CLASS}" title="{NAME}">
                    <div class="icon fa fa-circle"></div>
                </div>
                <!-- / -->
            </div>
            <div class="idle">
                <!-- importer/imported_datetime -->
                <div class="imported_datetime">
                    {CONTENT}
                </div>
                <!-- / -->
            </div>
            <div class="proc">
                <div class="progress">
                    <div class="bar"></div>
                    <div class="info">
                        <div class="position">&nbsp;</div>
                        <div class="percent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / -->

</div>
