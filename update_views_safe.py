import sys

def update_views():
    path = "G:/Antigravity/travel_voucher_app/vouchers/views.py"
    with open(path, 'r') as f:
        content = f.read()
    
    # 1. Add TestWebsiteSyncApiView to the end
    health_view = """
@method_decorator(csrf_exempt, name='dispatch')
class TestWebsiteSyncApiView(SuperuserRequiredMixin, View):
    def post(self, request, *args, **kwargs):
        is_local = request.get_host().startswith('127.0.0.1') or request.get_host().startswith('localhost')
        ping_url = 'https://leisurelooptrip.in/api-ping.php'
        if is_local:
             ping_url = 'http://localhost/leisure_loop_site/api-ping.php'
        try:
            response = requests.get(ping_url, timeout=5)
            if response.status_code == 200:
                return JsonResponse({'status': 'online', 'message': 'Marketing website is reachable.'})
            else:
                return JsonResponse({'status': 'issue', 'message': f'Website returned status {response.status_code}'})
        except Exception as e:
            return JsonResponse({'status': 'offline', 'message': 'Could not reach marketing website.', 'error_detail': str(e)})
"""
    if "class TestWebsiteSyncApiView" not in content:
        content += health_view

    # 2. Update SystemSettingsView form_valid
    old_valid = """    def form_valid(self, form):
        messages.success(self.request, "System settings updated successfully.")
        return super().form_valid(form)"""
    
    new_valid = """    def form_valid(self, form):
        action = self.request.POST.get('action')
        if action == 'regenerate_website_key':
            obj = self.get_object()
            new_key = obj.regenerate_website_key()
            messages.success(self.request, f"New Website API Key generated successfully.")
            return redirect("settings")
            
        messages.success(self.request, "System settings updated successfully.")
        return super().form_valid(form)"""
    
    content = content.replace(old_valid, new_valid)
    
    with open(path, 'w') as f:
        f.write(content)
    print("Successfully updated views.py")

if __name__ == "__main__":
    update_views()
