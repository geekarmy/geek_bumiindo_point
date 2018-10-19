<?php

namespace App\Model\Master;

use App\Model\MasterModel;

class Address extends MasterModel
{
    protected $connection = 'tenant';

    /**
     * Get all of the owning addressable models.
     */
    public function addressable()
    {
        return $this->morphTo();
    }

    public static function saveFromRelation($obj, $addresses)
    {
        // Delete address
        $ids = array_column($addresses, 'id');
        Address::where('addressable_id', $obj->id)
            ->where('addressable_type', get_class($obj))
            ->whereNotIn('id', $ids)->delete();

        for ($i = 0; $i < count($addresses); $i++) {
            // If address has id then update existing address
            // If not then create new address
            if (isset($addresses[$i]['id'])) {
                $address = Address::findOrFail($addresses[$i]['id']);
            } else {
                $address = new Address;
            }

            $address->address = $addresses[$i]['address'];
            $address->addressable_type = get_class($obj);
            $address->addressable_id = $obj->id;
            $address->save();
        }
    }
}
