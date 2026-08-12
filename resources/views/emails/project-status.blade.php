<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Project Status Update</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f5f5f5; padding:30px;">

    <div style="
        max-width:600px;
        margin:auto;
        background:white;
        padding:30px;
        border-radius:10px;
    ">

        <h2>Project Approval Workflow</h2>

        <p>
            Hello {{ $project->user->name }},
        </p>


        @if($status === 'submitted')

            <p>
                Your project has been successfully submitted for approval.
            </p>

            <p>
                <strong>Project:</strong>
                {{ $project->title }}
            </p>

            <p>
                <strong>Status:</strong>
                <span style="color:#f59e0b;">
                    Pending
                </span>
            </p>


        @elseif($status === 'approved')

            <p>
                Good news! Your project has been approved.
            </p>

            <p>
                <strong>Project:</strong>
                {{ $project->title }}
            </p>

            <p>
                <strong>Status:</strong>
                <span style="color:green;">
                    Approved
                </span>
            </p>


        @elseif($status === 'rejected')

            <p>
                Your project has been rejected.
            </p>

            <p>
                <strong>Project:</strong>
                {{ $project->title }}
            </p>

            <p>
                <strong>Status:</strong>
                <span style="color:red;">
                    Rejected
                </span>
            </p>

            @if($reason)

                <p>
                    <strong>Reason:</strong>
                </p>

                <div style="
                    background:#f8f8f8;
                    padding:15px;
                    border-left:4px solid red;
                ">
                    {{ $reason }}
                </div>

            @endif

        @endif


        <p style="margin-top:30px;">
            Regards,<br>
            Project Approval Team
        </p>

    </div>

</body>
</html>