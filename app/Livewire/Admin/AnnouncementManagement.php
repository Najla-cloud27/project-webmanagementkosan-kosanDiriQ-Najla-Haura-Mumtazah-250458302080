<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Announcoments;

class AnnouncementManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $title;
    public $content;
    public $image;
    public $publish_status = 'draf';
    public $editingId;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'image' => 'nullable|image|max:2048',
        'publish_status' => 'required|in:draf,diterbitkan',
    ];

    public function createAnnouncement()
    {
        $this->validate();

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('announcements', 'public');
        }

        Announcoments::create([
            'admin_id' => \Illuminate\Support\Facades\Auth::id(),
            'title' => $this->title,
            'content' => $this->content,
            'image_url' => $imagePath,
            'publish_status' => $this->publish_status,
        ]);

        session()->flash('success', 'Pengumuman berhasil dibuat!');
        $this->reset(['title', 'content', 'image', 'publish_status']);
    }

    public function editAnnouncement($id)
    {
        $announcement = Announcoments::findOrFail($id);
        $this->editingId = $id;
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->publish_status = $announcement->publish_status;
    }

    public function updateAnnouncement()
    {
        $this->validate();

        $announcement = Announcoments::findOrFail($this->editingId);

        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'publish_status' => $this->publish_status,
        ];

        if ($this->image) {
            $data['image_url'] = $this->image->store('announcements', 'public');
        }

        $announcement->update($data);

        session()->flash('success', 'Pengumuman berhasil diupdate!');
        $this->reset(['title', 'content', 'image','editingId']);
        $this->publish_status = 'draf';
    }

    public function deleteAnnouncement($id)
    {
        Announcoments::findOrFail($id)->delete();
        session()->flash('success', 'Pengumuman berhasil dihapus!');
    }

    public function render()
    {
        $announcements = Announcoments::with('user')->latest()->paginate(10);
        return view('livewire.admin.announcement-management', compact('announcements'));
    }
}