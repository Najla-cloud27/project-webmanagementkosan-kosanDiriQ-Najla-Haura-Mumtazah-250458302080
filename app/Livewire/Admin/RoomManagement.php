<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Rooms;
use App\Models\RoomImage;
use Illuminate\Support\Facades\Storage;

class RoomManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $showModal = false;
    public $name, $description, $price, $size, $status = 'tersedia', $fasilitas, $stok = 1;
    public $image;
    public $currentImage;
    public $search = '';
    public $editMode = false;
    public $roomId;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'size' => 'nullable|string|max:50',
        'status' => 'required|in:tersedia,terisi,perawatan,sudah_dipesan',
        'fasilitas' => 'nullable|string',
        'stok' => 'required|integer|min:0',
        'image' => 'nullable|image|max:2048',
    ];

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    public function closeModal()
    {
        $this->dispatch('modal-closed');
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->size = '';
        $this->status = 'tersedia';
        $this->fasilitas = '';
        $this->stok = 1;
        $this->image = null;
        $this->currentImage = null;
        $this->editMode = false;
        $this->roomId = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'size' => $this->size,
            'status' => $this->status,
            'fasilitas' => $this->fasilitas,
            'stok' => $this->stok,
        ];

        // Upload new image if exists
        if ($this->image) {
            // Delete old image if exists (edit mode)
            if ($this->editMode && $this->currentImage) {
                if (Storage::disk('public')->exists($this->currentImage)) {
                    Storage::disk('public')->delete($this->currentImage);
                }
            }
            
            $path = $this->image->store('room-images', 'public');
            $data['main_image_url'] = $path;
        }

        if ($this->editMode) {
            $room = Rooms::find($this->roomId);
            $room->update($data);
            $message = 'Kamar berhasil diupdate.';
        } else {
            $room = Rooms::create($data);
            $message = 'Kamar berhasil ditambahkan.';
        }

        session()->flash('message', $message);
        $this->closeModal();
    }

    public function edit($id)
    {
        $room = Rooms::findOrFail($id);
        $this->roomId = $id;
        $this->name = $room->name;
        $this->description = $room->description;
        $this->price = $room->price;
        $this->size = $room->size;
        $this->status = $room->status;
        $this->fasilitas = $room->fasilitas;
        $this->stok = $room->stok;
        $this->currentImage = $room->main_image_url;
        $this->editMode = true;
        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    public function deleteImage()
    {
        if ($this->editMode && $this->roomId) {
            $room = Rooms::find($this->roomId);
            
            if ($room->main_image_url) {
                // Delete file from storage
                if (Storage::disk('public')->exists($room->main_image_url)) {
                    Storage::disk('public')->delete($room->main_image_url);
                }
                
                // Update database
                $room->update(['main_image_url' => null]);
                $this->currentImage = null;
                
                session()->flash('message', 'Gambar berhasil dihapus.');
            }
        }
    }

    public function delete($id)
    {
        $room = Rooms::find($id);
        
        // Delete image if exists
        if ($room->main_image_url) {
            if (Storage::disk('public')->exists($room->main_image_url)) {
                Storage::disk('public')->delete($room->main_image_url);
            }
        }
        
        // Delete room
        $room->delete();
        session()->flash('message', 'Kamar berhasil dihapus.');
    }

    public function render()
    {
        $rooms = Rooms::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.admin.room-management', [
            'rooms' => $rooms
        ]);
    }
}