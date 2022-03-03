<?php

namespace format_topcoll\output\courseformat\content\section;

class cmlist extends \core_courseformat\output\local\content\section\cmlist {

    public function get_template_name(\renderer_base $renderer): string {
        return 'format_topcoll/local/content/section/cmlist';
    }
}
