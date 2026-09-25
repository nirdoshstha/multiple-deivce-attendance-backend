 <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Leave Application Status</title>
</head>

<body style="margin:0; padding:0; font-family:Arial, sans-serif; background:#f5f5f5;">

    <div style="padding:20px;">

        <div style="
            background-color:#28a745;
            color:#ffffff;
            padding:15px;
            text-align:center;
        ">
            <h2 style="margin:0;">
                Leave Application Status
            </h2>
        </div>

        <div style="
            background:#ffffff;
            padding:20px;
            margin-top:10px;
        ">

            <p>
                Hello <strong>{{ $detail['name'] }}</strong>,
            </p>

            <p>
                Your leave application has been
                @if($detail['is_approved']==1)
                    <strong style="color:#28a745;">approved</strong>.
                @elseif($detail['is_approved']==2)
                    <strong style="color:#dc3545;">rejected</strong>.
                @endif
            </p>

            <table width="100%" cellpadding="0" cellspacing="0"
                style="border-collapse:collapse; margin-top:20px;">

                <tr>
                    <th style="
                        border:1px solid #ddd;
                        padding:10px;
                        background:#cccccc;
                        color:#3b3b3b;
                        text-align:left;
                        width:30%;
                    ">
                        Full Name
                    </th>

                    <td style="border:1px solid #ddd; padding:10px;">
                        {{ $detail['name'] }}
                    </td>
                </tr>

                <tr>
                    <th style="
                        border:1px solid #ddd;
                        padding:10px;
                        background:#cccccc;
                        color:#3b3b3b;
                        text-align:left;
                    ">
                        Email
                    </th>

                    <td style="border:1px solid #ddd; padding:10px;">
                        {{ $detail['email'] }}
                    </td>
                </tr>

                <tr>
                    <th style="
                        border:1px solid #ddd;
                        padding:10px;
                        background:#cccccc;
                        color:#3b3b3b;
                        text-align:left;
                    ">
                        Staff Role
                    </th>

                    <td style="border:1px solid #ddd; padding:10px;">
                        {{ $detail['staff'] ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <th style="
                        border:1px solid #ddd;
                        padding:10px;
                        background:#cccccc;
                        color:#3b3b3b;
                        text-align:left;
                    ">
                        Approval Remarks
                    </th>

                    <td style="border:1px solid #ddd; padding:10px;">
                        {{ $detail['approval_remarks'] ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <th style="
                        border:1px solid #ddd;
                        padding:10px;
                        background:#cccccc;
                        color:#3b3b3b;
                        text-align:left;
                    ">
                        Status
                    </th>

                    <td style="border:1px solid #ddd; padding:10px;">
                       @if($detail['is_approved'] == 1)
                        <span style="color:#28a745;">
                            Approved
                        </span>
                    @elseif($detail['is_approved'] == 2)
                        <span style="color:#dc3545;">
                            Rejected
                        </span>
                         @elseif($detail['is_approved'] == 0)
                        <span style="color:#3185bd;">
                            Pending
                        </span>
                    @endif

                    </td>
                </tr>

            </table>

            <p style="margin-top:20px;">
                Thank you.
            </p>

        </div>

    </div>

</body>
</html>
