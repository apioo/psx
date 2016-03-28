<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Model\ActivityStream;

use PSX\Schema\RevealerInterface;

/**
 * ObjectType
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ObjectTypeRevealer implements RevealerInterface
{
    public function reveal($value, $path)
    {
        $types = $this->getTypes();
        $type  = $this->getType($value, $path);

        return $types[$type];
    }

    public function getType($value, $path)
    {
        $type = '';
        if (isset($value->objectType) && !empty($value->objectType)) {
            $type = $value->objectType;
            if ($type instanceof \stdClass) {
                $type = isset($type->id) ? $type->id : null;
            } else {
                $type = (string) $type;
            }
        }

        $types = $this->getTypes();

        if (isset($types[$type])) {
            return $type;
        } else {
            return 'objectType';
        }
    }

    public function getTypes()
    {
        return [
            'activity'   => 'PSX\\Model\\ActivityStream\\Activity',
            'collection' => 'PSX\\Model\\ActivityStream\\Collection',
            'objectType' => 'PSX\\Model\\ActivityStream\\ObjectType',
            'audio'      => 'PSX\\Model\\ActivityStream\\ObjectType\\Audio',
            'binary'     => 'PSX\\Model\\ActivityStream\\ObjectType\\Binary',
            'event'      => 'PSX\\Model\\ActivityStream\\ObjectType\\Event',
            'group'      => 'PSX\\Model\\ActivityStream\\ObjectType\\Group',
            'issue'      => 'PSX\\Model\\ActivityStream\\ObjectType\\Issue',
            'permission' => 'PSX\\Model\\ActivityStream\\ObjectType\\Permission',
            'place'      => 'PSX\\Model\\ActivityStream\\ObjectType\\Place',
            'role'       => 'PSX\\Model\\ActivityStream\\ObjectType\\Role',
            'task'       => 'PSX\\Model\\ActivityStream\\ObjectType\\Task',
            'video'      => 'PSX\\Model\\ActivityStream\\ObjectType\\Video',
        ];
    }
}