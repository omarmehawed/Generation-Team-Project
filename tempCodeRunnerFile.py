import pandas as pd

# Load the uploaded file to check headers
df = pd.read_csv('users_import_template (2).csv')
print(df.columns.tolist())
print(df.head())
import pandas as pd
import random

roles = ['student'] * 30 + ['doctor'] * 10 + ['ta'] * 10
random.shuffle(roles)

data = []
for i, role in enumerate(roles, 1):
    name = f"Test User {i}"
    email = f"user{i}@test.com"
    password = "password123"
    
    if role == 'student':
        year = random.choice([1, 2, 3, 4])
        dept = random.choice(['general', 'software', 'network'])
    else:
        # For staff, we can leave year empty or put a default if required. 
        # To be safe based on previous code snippets (departments exist for staff), let's assign dept but no year.
        year = '' 
        dept = random.choice(['general', 'software', 'network'])

    row = {
        'Name': name,
        'Email': email,
        'Role (student/doctor/ta)': role,
        'Year (1-4)': year,
        'Department (general/software/network)': dept,
        'Password (Optional)': password
    }
    data.append(row)

df_50 = pd.DataFrame(data)
output_file = 'dummy_users_50.csv'
df_50.to_csv(output_file, index=False)

print(f"File {output_file} created with {len(df_50)} rows.")