<?php

namespace App\Models;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Person extends Model
{

    protected $table = 'persons';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'person_id');
    }

    public function syncContacts($contacts): void {
        
        if ($contacts) {
            foreach ($contacts as $contact) {
                if (!isset($contact['id'])) {
                    Contact::create([
                        'name' => $contact['name'],
                        'person_id' => $this->id
                    ]);
                } else {

                    $existContact = Contact::find($contact['id']);

                    if ($existContact) {
                        if (empty($contact['name'])) {
                            $existContact->delete();
                        } else {
                            $existContact->name = $contact['name'];
                            $existContact->update();
                        }
                    }
                }
            }
        }
    }

}
