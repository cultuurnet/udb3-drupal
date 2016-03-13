<?php

namespace Drupal\culturefeed_udb3;

use CultuurNet\UDB3\Iri\IriGeneratorInterface;

class LocalFileIriGenerator implements IriGeneratorInterface {

    /**
     * @inheritdoc
     */
    public function iri($item)
    {
        return file_create_url('public://downloads/' . $item);
    }

}
