import urllib.request
import urllib.parse

data = urllib.parse.urlencode({
    'name': 'Nanda',
    'phone': '8918921629',
    'email': 'leisurelooptrip@gmail.com',
    'company': 'Leisure Loop Trip',
    'size': '30',
    'destination': 'Sikkim',
    'requirements': 'I want full hotel and cab'
}).encode('utf-8')

req = urllib.request.Request('http://localhost/leisure_loop_site/api/submit-lead.php', data=data)
try:
    with urllib.request.urlopen(req) as response:
        print("Status:", response.status)
        print("Body:", response.read().decode('utf-8'))
except Exception as e:
    print("Error:", e)
    if hasattr(e, 'read'):
        print("Body:", e.read().decode('utf-8'))
