<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    <div class="top_bar">
        <div class="pagination">
            <div class="per_page">
                {PER_PAGE_SELECTOR}
            </div>
            <div class="paginator">
                {PAGINATOR}
            </div>
        </div>

        <div class="detect_importers_button">
            <div class="idle">
                <div class="icon fa fa-refresh"></div>
                <div class="label">Разпознать новые файлы</div>
            </div>
            <div class="proc">
                <div class="progress">
                    <div class="bar"></div>
                    <div class="info">
                        {*<span class="comment"></span>*}
                        <span class="position"></span>
                        <span class="percent"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="messages">
        <!-- message -->
        <tr class="message" message_id="{ID}">
            <td class="datetime" width="1">
                <div>{DATETIME}</div>
                <div>uid: {UID}</div>
            </td>
            <td class="info" title="{TITLE}">
                <div class="from">{FROM}</div>
                <div class="subject">{SUBJECT}</div>
            </td>
            <td class="attachments" width="1">
                <table>
                    <!-- message/attachment -->
                    <tr class="attachment {IMPORTED_CLASS}" xpack="{XPACK}">
                        <td>
                            {IMPORTER}
                        </td>
                        <td>
                            <a href="{DOWNLOAD_URL}" title="{ID}">{NAME}</a>
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
