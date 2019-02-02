<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    <table class="messages">
        <!-- message -->
        <tr class="message">
            <td class="datetime">{DATETIME}</td>
            <td class="info" title="{TITLE}">
                <div class="from">{FROM}</div>
                <div class="subject">{SUBJECT}</div>
            </td>
            <td class="attachments">
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

</div>
