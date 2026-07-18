<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{    
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index() {
        $breadcrumbs = [
            [
                'label' => 'Master',
                'url' => null,
            ],
            [
                'label' => 'User',
                'url' => route('master.user.index'),
            ],
        ];

        return [
            "breadcrumbs" => $breadcrumbs,
        ];
    }

    public function indexDataTable() {
        $query = $this->userRepository->query();

        return datatables()->of($query)
            ->addColumn('action', function($row){
                $showUrl = route('master.user.show', $row->id);
                $editUrl = route('master.user.edit', $row->id);
                
                return '
                    <div class="flex items-center justify-start gap-2">
                        <a href="'.$showUrl.'" class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-100 rounded-md transition-all duration-150" title="Detail User">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </a>
                        <a href="'.$editUrl.'" class="p-1.5 text-gray-400 hover:text-cyan-600 hover:bg-cyan-100 rounded-md transition-all duration-150" title="Edit User">
                            <i class="fa-solid fa-pen text-sm"></i>
                        </a>
                        <button type="button" onclick="deleteUser('.$row->id.', \''.e($row->name).'\')" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-all duration-150 cursor-pointer" title="Delete User">
                            <i class="fa-solid fa-trash text-sm"></i>
                        </button>
                    </div>';
            })
            ->editColumn('name', function($row) {
                $initials = strtoupper(substr($row->name, 0, 2));
                return '
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-cyan-600/10 text-cyan-700 flex items-center justify-center font-bold text-sm">
                            ' . $initials . '
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">' . e($row->name) . '</p>
                        </div>
                    </div>';
            })
            ->editColumn('created_at', function($row) {
                return $row->created_at ? $row->created_at->format('M d, Y H:i') : '';
            })
            ->rawColumns(['action', 'name', 'email_verified_at'])
            ->make(true);
    }

    public function create() {
        $breadcrumbs = [
            ['label' => 'Master', 'url' => null],
            ['label' => 'User', 'url' => route('master.user.index')],
            ['label' => 'Create User', 'url' => null],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    public function store(array $data) {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        return $this->userRepository->create($data);
    }

    public function edit(int $id) {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return null;
        }

        $breadcrumbs = [
            ['label' => 'Master', 'url' => null],
            ['label' => 'User', 'url' => route('master.user.index')],
            ['label' => 'Edit User', 'url' => null],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
            'user' => $user,
        ];
    }

    public function update(int $id, array $data) {
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        return $this->userRepository->update($id, $data);
    }

    public function delete(int $id) {
        return $this->userRepository->delete($id);
    }

    public function show(int $id) {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return null;
        }

        $breadcrumbs = [
            ['label' => 'Master', 'url' => null],
            ['label' => 'User', 'url' => route('master.user.index')],
            ['label' => 'Detail User', 'url' => null],
        ];

        return [
            'breadcrumbs' => $breadcrumbs,
            'user' => $user,
        ];
    }

    public function getUser(int $id) {
        return $this->userRepository->find($id);
    }
}
