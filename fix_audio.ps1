# Fix Audio Script - Restart audio services and driver
Write-Host "=== Fixing Audio Input ===" -ForegroundColor Cyan

# Step 1: Restart Audio Services
Write-Host "`n[1/4] Restarting Windows Audio Services..." -ForegroundColor Yellow
try {
    Restart-Service -Name AudioEndpointBuilder -Force -ErrorAction Stop
    Restart-Service -Name Audiosrv -Force -ErrorAction Stop
    Write-Host "  Audio services restarted successfully!" -ForegroundColor Green
} catch {
    Write-Host "  Warning: Could not restart services (may need admin): $_" -ForegroundColor Red
}

# Step 2: Restart Realtek Audio Driver
Write-Host "`n[2/4] Restarting Realtek Audio Driver..." -ForegroundColor Yellow
try {
    $device = Get-PnpDevice -FriendlyName '*Realtek(R) Audio*' -Class Media
    if ($device) {
        $device | Disable-PnpDevice -Confirm:$false -ErrorAction Stop
        Start-Sleep -Seconds 3
        $device | Enable-PnpDevice -Confirm:$false -ErrorAction Stop
        Write-Host "  Realtek Audio driver restarted!" -ForegroundColor Green
    }
} catch {
    Write-Host "  Warning: Could not restart driver (may need admin): $_" -ForegroundColor Red
}

# Step 3: Check microphone status
Write-Host "`n[3/4] Checking Microphone Status..." -ForegroundColor Yellow
$mics = Get-PnpDevice -Class AudioEndpoint | Where-Object { $_.FriendlyName -match 'Mic' }
foreach ($mic in $mics) {
    Write-Host "  $($mic.FriendlyName) - Status: $($mic.Status)" -ForegroundColor $(if ($mic.Status -eq 'OK') { 'Green' } else { 'Red' })
}

# Step 4: Check and set default recording device
Write-Host "`n[4/4] Audio Device Summary:" -ForegroundColor Yellow
Get-PnpDevice -Class AudioEndpoint | Format-Table Status, FriendlyName -AutoSize

Write-Host "`n=== Done! ===" -ForegroundColor Cyan
Write-Host "Please restart Discord and test your microphone." -ForegroundColor White
Write-Host "If the issue persists, try these manual steps:" -ForegroundColor White
Write-Host "  1. Right-click speaker icon > Sound Settings > Input" -ForegroundColor Gray
Write-Host "  2. Make sure correct microphone is selected" -ForegroundColor Gray
Write-Host "  3. In Discord: Settings > Voice & Video > Input Device" -ForegroundColor Gray
Write-Host "  4. Select your mic instead of 'Default'" -ForegroundColor Gray
