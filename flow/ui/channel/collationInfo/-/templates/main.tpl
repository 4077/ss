<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    <div class="top_bar">
        <div class="counts scroll_mode">
            <div class="count sources">{SOURCES_COUNT}</div>
            <div class="count middle">{CONNECTIONS_COUNT}</div>
            <div class="count targets">{TARGETS_COUNT}</div>
        </div>
    </div>

    <div class="content">
        <div class="sources products panel" type="source">
            <!-- source -->
            <div class="product {CONNECTED_CLASS}" n="{N}" product_id="{ID}">
                <div class="id">{ID}</div>
                <div class="field_value">{FIELD_VALUE}</div>
            </div>
            <!-- / -->
        </div>
        <div class="connections panel" type="connection">
            <!-- connection -->
            <div class="connection" n="{N}" source_id="{SOURCE_ID}" target_id="{TARGET_ID}">
                {SOURCE_ID}:{TARGET_ID}
            </div>
            <!-- / -->
        </div>
        <div class="targets products panel" type="target">
            <!-- target -->
            <div class="product {CONNECTED_CLASS}" n="{N}" product_id="{ID}">
                <div class="id">{ID}</div>
                <div class="field_value">{FIELD_VALUE}</div>
            </div>
            <!-- / -->
        </div>
    </div>

</div>
