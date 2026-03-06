<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
$_SESSION['registered_events'] = $_SESSION['registered_events'] ?? [];

$eventsFile = "events.json";
$events = [];

if (file_exists($eventsFile)) {
    $events = json_decode(file_get_contents($eventsFile), true);
    if (!is_array($events)) $events = [];
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "update_profile" && !empty($_FILES["profile_pic"]["name"])) {
        $allowed = ["jpg","jpeg","png","gif"];
        $ext = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        if (!in_array($ext,$allowed)) {
            $error = "Invalid image format.";
        } else {
            if (!is_dir("profiles")) mkdir("profiles",0777,true);
            $filename = $user["id"]."_".time().".".$ext;
            $path = "profiles/".$filename;
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"],$path)) {
                $_SESSION["user"]["profile_pic"] = $path;
                $message = "Profile updated successfully.";
            } else { $error = "Upload failed."; }
        }
    }

    if ($action === "cancel_event") {
        $event_id = intval($_POST["event_id"]);
        $_SESSION["registered_events"] = array_values(
            array_diff($_SESSION["registered_events"], [$event_id])
        );
        $message = "Registration cancelled.";
    }
}

$registered = [];
foreach ($events as $event) {
    if (in_array($event["id"], $_SESSION["registered_events"])) {
        $registered[] = $event;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard • EventReg</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    background-attachment: fixed;
    position: relative;
    overflow-x: hidden;
}
body::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: url('https://www.transparenttextures.com/patterns/diagmonds.png');
    opacity: 0.05;
    z-index: -1;
}

.card-hover{
transition: all .35s ease;
}
.card-hover:hover{
transform: translateY(-8px) scale(1.02);
box-shadow: 0 20px 50px rgba(0,0,0,0.5);
}

.btn-gradient {
    background: linear-gradient(90deg,#6366f1,#a855f7);
    transition: all 0.3s ease;
}
.btn-gradient:hover { opacity: 0.9; }
</style>
</head>

<body class="text-white">

<div class="max-w-7xl mx-auto px-6 py-12">

<!-- HEADER -->
<div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-4">
<h1 class="text-4xl font-bold">My Dashboard</h1>
<div class="flex gap-4">
<a href="index.html" class="px-6 py-3 rounded-2xl btn-gradient font-semibold">Discover Events</a>
<form method="POST" action="logout.php">
<button class="px-6 py-3 rounded-2xl bg-red-600 hover:bg-red-500 font-semibold transition">Logout</button>
</form>
</div>
</div>

<!-- ALERTS -->
<?php if($message): ?>
<div class="bg-green-500/20 border border-green-400 text-green-300 p-4 rounded-xl mb-8"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if($error): ?>
<div class="bg-red-500/20 border border-red-400 text-red-300 p-4 rounded-xl mb-8"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- PROFILE CARD -->
<div class="flex flex-col md:flex-row items-center md:items-start bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-10 mb-16 shadow-lg gap-8">
<img src="<?= htmlspecialchars($user["profile_pic"] ?? "https://ui-avatars.com/api/?name=".urlencode($user["name"])) ?>" class="w-36 h-36 rounded-3xl border-4 border-indigo-600 object-cover shadow-lg">
<div class="flex-1">
<h2 class="text-3xl font-bold mb-2"><?= htmlspecialchars($user["name"]) ?></h2>
<p class="text-zinc-400 mb-6"><?= htmlspecialchars($user["email"]) ?></p>
<form method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-4">
<input type="file" name="profile_pic" class="bg-white text-black px-4 py-2 rounded-xl">
<input type="hidden" name="action" value="update_profile">
<button class="px-6 py-3 btn-gradient rounded-2xl font-semibold transition">Update Picture</button>
</form>
</div>
</div>

<!-- REGISTERED EVENTS -->
<div class="flex justify-between items-center mb-8">
<h2 class="text-3xl font-bold">My Registered Events</h2>
</div>

<?php if(!$registered): ?>
<div class="text-center py-16 text-zinc-400">
<p class="text-xl mb-6">You haven't registered for any events yet.</p>
<a href="index.html" class="px-8 py-4 btn-gradient rounded-2xl font-semibold transition">Discover Events</a>
</div>
<?php else: ?>
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
<?php foreach($registered as $event): ?>
<div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-3xl overflow-hidden card-hover">
<img src="<?= htmlspecialchars($event["image"]) ?>" class="w-full h-52 object-cover">
<div class="p-8">
<h3 class="text-xl font-bold mb-3"><?= htmlspecialchars($event["title"]) ?></h3>
<p class="text-zinc-400 mb-6"><?= htmlspecialchars($event["location"]) ?><br><?= htmlspecialchars($event["date"]) ?></p>
<form method="POST">
<input type="hidden" name="action" value="cancel_event">
<input type="hidden" name="event_id" value="<?= $event["id"] ?>">
<button class="w-full py-3 bg-red-600 hover:bg-red-500 rounded-2xl font-semibold transition">Cancel Registration</button>
</form>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

</div>

</body>
</html>