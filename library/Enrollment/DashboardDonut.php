<?php

/* Icinga DB Web | (c) 2020 Icinga GmbH | GPLv2 */

namespace Icinga\Module\Enrollment;

use Icinga\Chart\Donut;
use ipl\Html\Attributes;
use ipl\Html\BaseHtmlElement;
use ipl\Html\HtmlElement;
use ipl\Html\HtmlString;
use ipl\Html\TemplateString;
use ipl\Html\Text;
use ipl\Stdlib\BaseFilter;
use ipl\Web\Common\Card;

class DashboardDonut extends Card
{
    use BaseFilter;

    protected $defaultAttributes = ['class' => 'donut-container', 'data-base-target' => '_next'];

    protected $summary;

    public function __construct($summary)
    {
        $this->summary = $summary;
    }

    protected function assembleBody(BaseHtmlElement $body)
    {

        $donut = (new Donut())
            ->addSlice($this->summary->good, ['class' => 'slice-state-ok'])
            ->addSlice($this->summary->bad, ['class' => 'slice-state-critical'])
            ->setLabelBig($this->summary->bad)
            ->setLabelBigEyeCatching($this->summary->bad > 0)
            ->setLabelSmall($this->summary->labelSmall);
        if(isset($this->summary->labelUrl)){
            $donut->setLabelBigUrl($this->summary->labelUrl);
        }


        $body->addHtml(
            new HtmlElement('div', Attributes::create(['class' => 'donut']), new HtmlString($donut->render()))
        );
    }

    protected function assembleFooter(BaseHtmlElement $footer)
    {
    }

    protected function assembleHeader(BaseHtmlElement $header)
    {
        $header->addHtml(
            new HtmlElement('h2', null, Text::create($this->summary->title)),
            new HtmlElement('span', Attributes::create(['class' => 'meta']), TemplateString::create(
                t('{{#total}}Total{{/total}} %d'),
                ['total' => new HtmlElement('span')],
                (int) $this->summary->bad + (int) $this->summary->good
            ))
        );
    }
}
