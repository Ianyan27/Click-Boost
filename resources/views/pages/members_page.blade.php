@extends('layouts.app')

@section('content')
<div class="clickup-container">
    <div class="member-container">
        <div class="button-section-container">
            <div>
                <span class="screen-title">Members</span>
            </div>
        </div>
        <div class="clickup-table-container">
            <table>
                <thead>
                    <tr>
                        <th colspan="7">Username</th>
                        <th colspan="3">Email</th>
                        <th colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                        <tr>
                            <td colspan="7">{{ $member->username}}</td>
                            <td colspan="3">{{ $member->email }}</td>
                            <td class="action-column" colspan="2">
                                <div class="action-buttons">
                                    <button>
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection