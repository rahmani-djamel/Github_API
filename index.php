
<?php

// Function to make API requests
function githubRequest($method, $url, $data = []) {
    $accessToken = '2b62e2e23f9b342c6c6eb58f24249def538dc8ca';
    $apiUrl = 'https://api.github.com/';

    $ch = curl_init($apiUrl . $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: PHP',
        'Authorization: token ' . $accessToken,
        'Content-Type: application/json',
    ]);
    
    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Push code to a repository
function pushToGitHub($repository, $branch, $filePath, $commitMessage, $content) {
    $url = "repos/{$repository}/contents/{$filePath}";
    
    $existingFile = githubRequest('GET', $url);
    
    if ($existingFile && isset($existingFile['sha'])) {
        // File already exists, update it
        $data = [
            'message' => $commitMessage,
            'content' => base64_encode($content),
            'sha' => $existingFile['sha'],
            'branch' => $branch,
        ];
        
        return githubRequest('PUT', $url, $data);
    } else {
        // File doesn't exist, create it
        $data = [
            'message' => $commitMessage,
            'content' => base64_encode($content),
            'branch' => $branch,
        ];
        
        return githubRequest('PUT', $url, $data);
    }
}

// Pull code from a repository
function pullFromGitHub($repository, $branch, $filePath) {
    $url = "repos/{$repository}/contents/{$filePath}?ref={$branch}";
    
    return githubRequest('GET', $url);
}

// Example usage
$repository = 'Github_API';
$branch = 'YOUR_BRANCH';
$filePath = 'path/to/file.txt';
$commitMessage = 'Commit message';
$content = 'Content to push';

// Push code to GitHub
pushToGitHub($repository, $branch, $filePath, $commitMessage, $content);

// Pull code from GitHub
pullFromGitHub($repository, $branch, $filePath);

?>