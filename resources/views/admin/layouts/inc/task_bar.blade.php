@php
$user_id = Auth::id();
$tasks = \App\Task::where('assign_to', $user_id)->whereNotIn('status', ['Completed'])->whereRaw(" NOW() BETWEEN tasks.start_date AND tasks.end_date")->get();
$_task_status = collect($tasks)->pluck('status', 'status');

@endphp
<div class="kt-header__topbar-item dropdown">
    <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="30px,0px" aria-expanded="true">
        <span class="kt-header__topbar-icon">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <rect id="bound" x="0" y="0" width="24" height="24"/>
                    <path d="M5.5,2 L18.5,2 C19.3284271,2 20,2.67157288 20,3.5 L20,6.5 C20,7.32842712 19.3284271,8 18.5,8 L5.5,8 C4.67157288,8 4,7.32842712 4,6.5 L4,3.5 C4,2.67157288 4.67157288,2 5.5,2 Z M11,4 C10.4477153,4 10,4.44771525 10,5 C10,5.55228475 10.4477153,6 11,6 L13,6 C13.5522847,6 14,5.55228475 14,5 C14,4.44771525 13.5522847,4 13,4 L11,4 Z" id="Combined-Shape" fill="#000000" opacity="0.3"/>
                    <path d="M5.5,9 L18.5,9 C19.3284271,9 20,9.67157288 20,10.5 L20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 L4,10.5 C4,9.67157288 4.67157288,9 5.5,9 Z M11,11 C10.4477153,11 10,11.4477153 10,12 C10,12.5522847 10.4477153,13 11,13 L13,13 C13.5522847,13 14,12.5522847 14,12 C14,11.4477153 13.5522847,11 13,11 L11,11 Z M5.5,16 L18.5,16 C19.3284271,16 20,16.6715729 20,17.5 L20,20.5 C20,21.3284271 19.3284271,22 18.5,22 L5.5,22 C4.67157288,22 4,21.3284271 4,20.5 L4,17.5 C4,16.6715729 4.67157288,16 5.5,16 Z M11,18 C10.4477153,18 10,18.4477153 10,19 C10,19.5522847 10.4477153,20 11,20 L13,20 C13.5522847,20 14,19.5522847 14,19 C14,18.4477153 13.5522847,18 13,18 L11,18 Z" id="Combined-Shape" fill="#000000"/>
                </g>
            </svg>
        </span>
    </div>
    <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">
        <form>
            <!-- begin:: Mycart -->
            <div class="kt-mycart">
                <div class="kt-mycart__head kt-head" style="background-image: url({{ asset_url('media/misc/bg-1.jpg', 1) }});">
                    <div class="kt-mycart__info">
                        <span class="kt-mycart__icon"><i class="flaticon2-list-3 kt-font-success"></i></span>
                        <h3 class="kt-mycart__title">My Task</h3>
                    </div>
                    <div class="kt-mycart__button">
                        <button type="button" class="btn btn-success btn-sm" style=" ">{{ number_format(count($tasks)) }} Tasks</button>
                    </div>
                </div>

                <div class="kt-mycart__body kt-scroll" data-scroll="true" data-height="320" data-mobile-height="200">
                    @foreach($tasks as $task)
                        <div class="kt-mycart__item">
                            <div class="kt-mycart__container">
                                <div class="kt-mycart__info">
                                    <a href="#" class="kt-mycart__title">{{ $task->task }}</a>
                                    <span class="kt-mycart__desc">{{ $task->description }}</span>

                                    <div class="kt-mycart__action">
                                        <span class="kt-mycart__price">{{ $task->start_date }} - {{ $task->end_date }}</span>
                                    </div>
                                </div>

                                <div class="kt-mycart__pic text-center">
                                    <p>{{ $task->estimate_time }} <span class='kt-badge kt-badge--danger kt-badge--inline'>Hour's</span></p>
                                    <a class="start_task" data-task_id="{{ $task->id }}" data-time="{{ $task->estimate_time }}" data-start_date="{{ $task->start_date }}" data-end_date="{{ $task->end_date }}" data-skin="dark" data-toggle="kt-tooltip" data-original-title="Start Task" href="{{ admin_url("briefcase/ajax/update_task/{$task->id}/?status=Start") }}"><i class="flaticon-stopwatch fa-2x"></i></a>
                                    <a data-skin="dark" data-toggle="kt-tooltip" data-original-title="View Task" href="{{ admin_url("briefcase/view/{$task->id}") }}"><i class="flaticon-clipboard fa-2x"></i></a>
                                </div>
                            </div>
                            <div class="kt-widget__container kt-padding-15">
                                <span class="kt-widget__subtitel">Progress</span>
                                <div class="kt-widget__progress d-flex align-items-center flex-fill">
                                    <div class="progress" style="height: 5px;width: 100%;">
                                        <div class="progress-bar kt-bg-success" role="progressbar" style="width: 78%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="kt-widget__stat">78%</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
            <!-- end:: Mycart -->
        </form>
    </div>
</div>
