<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    <div class="tree_selector">
        <div class="targets">
            <!-- target -->
            <div class="target">
                {SELECT_BUTTON}
                {SELECT_CONNECTION_BUTTON}
            </div>
            <!-- / -->
        </div>

        <div class="selected">
            <div class="dropdown hidden">
                <!-- tree -->
                {BUTTON}
                <!-- / -->
            </div>
            <div class="name">
                <i class="icon fa fa-{SELECTED_ICON}"></i>
                {NAME}
            </div>
        </div>

        <div class="sources">
            <!-- source -->
            <div class="source">
                {SELECT_CONNECTION_BUTTON}
                {SELECT_BUTTON}
            </div>
            <!-- / -->
        </div>
    </div>

    <div class="editor">
        <div class="tabs">
            <!-- tab -->
            {BUTTON}
            <!-- / -->
        </div>

        <div class="panels">
            <div class="st panel">
                <div class="content">
                    <div class="header">{TARGET} ←</div>
                    <div class="cp">{ST_CONTENT}</div>
                </div>
            </div>
            <div class="ts panel">
                <div class="content">
                    <div class="header">→ {SOURCE}</div>
                    <div class="cp">{TS_CONTENT}</div>
                </div>
            </div>
        </div>
    </div>

</div>
