@php
    $task = $followUp->task ?? null;
    $reminderTime = $followUp->reminder_at
        ? $followUp->reminder_at->timezone(config('app.timezone'))->format('d M Y, H:i')
        : '-';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Task</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif; color: #111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f3f4f6; margin: 0; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 640px; overflow: hidden; border-radius: 16px; background-color: #ffffff; border: 1px solid #e5e7eb;">
                    <tr>
                        <td style="padding: 28px 32px; background-color: #2563eb;">
                            <p style="margin: 0 0 8px; color: #bfdbfe; font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">
                                Second Brain Reminder
                            </p>
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; line-height: 1.3; font-weight: 700;">
                                Waktunya follow up task
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 20px; color: #4b5563; font-size: 16px; line-height: 1.6;">
                                Kamu punya follow-up yang sudah dijadwalkan. Berikut detailnya:
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse: separate; border-spacing: 0; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 18px 20px; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <p style="margin: 0 0 6px; color: #6b7280; font-size: 13px; font-weight: 700; text-transform: uppercase;">
                                            Follow Up
                                        </p>
                                        <p style="margin: 0; color: #111827; font-size: 20px; line-height: 1.4; font-weight: 700;">
                                            {{ $followUp->title }}
                                        </p>
                                    </td>
                                </tr>

                                @if ($task)
                                    <tr>
                                        <td style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
                                            <p style="margin: 0 0 6px; color: #6b7280; font-size: 13px; font-weight: 700;">
                                                Task
                                            </p>
                                            <p style="margin: 0; color: #374151; font-size: 15px; line-height: 1.5;">
                                                {{ $task->title }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td style="padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
                                        <p style="margin: 0 0 6px; color: #6b7280; font-size: 13px; font-weight: 700;">
                                            Waktu Reminder
                                        </p>
                                        <p style="margin: 0; color: #2563eb; font-size: 16px; line-height: 1.5; font-weight: 700;">
                                            {{ $reminderTime }}
                                        </p>
                                    </td>
                                </tr>

                                @if ($followUp->description || $followUp->note)
                                    <tr>
                                        <td style="padding: 16px 20px;">
                                            <p style="margin: 0 0 6px; color: #6b7280; font-size: 13px; font-weight: 700;">
                                                Keterangan
                                            </p>
                                            <p style="margin: 0; color: #374151; font-size: 15px; line-height: 1.6;">
                                                {{ $followUp->description ?? $followUp->note }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            <div style="margin-top: 24px; padding: 16px 18px; border-radius: 12px; background-color: #eff6ff; border: 1px solid #bfdbfe;">
                                <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 1.6;">
                                    Buka Second Brain untuk menyelesaikan follow-up ini atau menambahkan catatan lanjutan.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 32px; background-color: #f9fafb; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.5;">
                                Email ini dikirim otomatis oleh Second Brain.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
