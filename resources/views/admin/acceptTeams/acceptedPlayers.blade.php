<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Players List</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 700px;
            width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 14px;
            background-color: #fafafa;
            transition: background-color 0.3s ease;
            cursor: default;
        }

        li:hover {
            background-color: #e7f0ff;
        }

        li div strong {
            display: block;
            font-size: 1.15rem;
            color: #222;
            margin-bottom: 4px;
        }

        li div small {
            color: #666;
            font-size: 0.9rem;
        }

        svg {
            width: 30px;
            height: 30px;
            fill: #0d6efd;
            flex-shrink: 0;
        }

        .back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 28px;
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            user-select: none;
        }

        .back-btn:hover,
        .back-btn:focus {
            background-color: #084bcc;
            outline: none;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px 20px;
            }

            li {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            svg {
                align-self: flex-end;
            }
        }
    </style>
</head>

<body>
    <div class="container" style="margin-top: 200px">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="color: #3b1e54; margin-bottom: 20px;">Event: {{ $event->title }}</h2>
            <h2 style="color: #3b1e54; margin-bottom: 20px;">Category: {{ $category->name }}</h2>
        </div>

        <ul>
            @foreach ($players as $player)
                @php
                    $name = $player->user ? $player->user->name : 'No Name Available';
                    $gender = $player->user ? $player->user->gender : 'No Gender Available';
                    $birthDate =
                        $player->user && $player->user->birth_date
                            ? \Carbon\Carbon::parse($player->user->birth_date)
                            : null;
                    $formattedBirthDate = $birthDate ? $birthDate->format('M d, Y') : 'No Birth Date Available';
                    $age = $birthDate ? $birthDate->age : null;
                @endphp
                <li>
                    <div>
                        <div style="display: flex; gap: 8px;"><strong>{{ $name }}</strong>,
                            <strong>{{ $gender }}</strong>
                        </div>

                        <small>
                            {{ $formattedBirthDate }}
                            @if ($age)
                                &middot; Age: {{ $age }}
                            @endif
                        </small>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    </svg>
                </li>
            @endforeach
        </ul>
        <a href="{{ route('categoryTeams', $event->id) }}" class="back-btn" tabindex="0">← Back to Teams
        </a>
    </div>
</body>

</html>
