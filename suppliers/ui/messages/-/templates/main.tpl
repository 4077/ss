<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    <div class="pagination">
        <div class="per_page">
            {PER_PAGE_SELECTOR}
        </div>
        <div class="paginator">
            {PAGINATOR}
        </div>
    </div>

    <table class="messages">
        <!-- message -->
        <tr class="message">
            <td class="datetime" width="1">{DATETIME}</td>
            <td class="info" title="{TITLE}">
                <div class="from">{FROM}</div>
                <div class="subject">{SUBJECT}</div>
            </td>
            <td class="attachments" width="1">
                <table>
                    <!-- message/attachment -->
                    <tr class="attachment {IMPORTED_CLASS}" file_code="{FILE_CODE}" title="{TITLE}">
                        <td>
                            <div class="importer {IMPORTER_CLASS}">
                                <div class="name">{IMPORTER_NAME}</div>
                                <div class="progress_bar"></div>
                            </div>
                        </td>
                        <td>
                            <a href="{DOWNLOAD_URL}">{NAME}</a>
                        </td>
                    </tr>
                    <!-- / -->
                </table>
            </td>
        </tr>
        <!-- / -->
    </table>

    <div class="pagination">
        <div class="per_page">
            {PER_PAGE_SELECTOR}
        </div>
        <div class="paginator">
            {PAGINATOR}
        </div>
    </div>

</div>
