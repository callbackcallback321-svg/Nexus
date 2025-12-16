#!/usr/bin/env python3
"""
News Template Manager - Add, Edit, Delete News Items
"""

import json
import os
from datetime import datetime
from colorama import Fore, Style, init

init(autoreset=True)

CONFIG_FILE = "news_config.json"

def load_config():
    """Load news configuration from JSON file"""
    if not os.path.exists(CONFIG_FILE):
        # Create default config if it doesn't exist
        default_config = {
            "breaking_news": [],
            "live_news": [],
            "news_location": []
        }
        save_config(default_config)
        return default_config
    
    try:
        with open(CONFIG_FILE, 'r', encoding='utf-8') as f:
            return json.load(f)
    except Exception as e:
        print(f"{Fore.RED}Error loading config: {e}")
        return {"breaking_news": [], "live_news": [], "news_location": []}

def save_config(config):
    """Save news configuration to JSON file"""
    try:
        with open(CONFIG_FILE, 'w', encoding='utf-8') as f:
            json.dump(config, f, indent=2, ensure_ascii=False)
        return True
    except Exception as e:
        print(f"{Fore.RED}Error saving config: {e}")
        return False

def list_news(config, template_type):
    """List all news items for a template type"""
    if template_type not in config:
        print(f"{Fore.RED}Invalid template type: {template_type}")
        return
    
    news_items = config[template_type]
    if not news_items:
        print(f"{Fore.YELLOW}No news items found for {template_type}")
        return
    
    print(f"\n{Fore.CYAN}{'='*60}")
    print(f"{Fore.GREEN}News Items for: {template_type.upper().replace('_', ' ')}")
    print(f"{Fore.CYAN}{'='*60}\n")
    
    for item in news_items:
        print(f"{Fore.YELLOW}ID: {item['id']}")
        print(f"{Fore.WHITE}Title: {item['title']}")
        print(f"{Fore.CYAN}Content: {item['content'][:100]}..." if len(item['content']) > 100 else f"{Fore.CYAN}Content: {item['content']}")
        print(f"{Fore.MAGENTA}Timestamp: {item.get('timestamp', 'N/A')}")
        print(f"{Fore.CYAN}{'-'*60}\n")

def add_news(config, template_type):
    """Add a new news item"""
    if template_type not in config:
        print(f"{Fore.RED}Invalid template type: {template_type}")
        print(f"{Fore.YELLOW}Available types: breaking_news, live_news, news_location")
        return
    
    print(f"\n{Fore.GREEN}Adding new news item to: {template_type}")
    print(f"{Fore.CYAN}{'='*60}\n")
    
    # Get next ID
    existing_ids = [item['id'] for item in config[template_type]] if config[template_type] else [0]
    new_id = max(existing_ids) + 1 if existing_ids else 1
    
    title = input(f"{Fore.YELLOW}Enter news title: {Fore.WHITE}")
    if not title:
        print(f"{Fore.RED}Title cannot be empty!")
        return
    
    print(f"{Fore.YELLOW}Enter news content (press Enter twice to finish):")
    content_lines = []
    while True:
        line = input()
        if line == "" and content_lines and content_lines[-1] == "":
            break
        content_lines.append(line)
    
    content = "\n".join(content_lines).strip()
    if not content:
        print(f"{Fore.RED}Content cannot be empty!")
        return
    
    timestamp = input(f"{Fore.YELLOW}Enter timestamp (e.g., 'Just Now', '5 minutes ago'): {Fore.WHITE}") or "Just Now"
    
    new_item = {
        "id": new_id,
        "title": title,
        "content": content,
        "timestamp": timestamp
    }
    
    # Add template-specific fields
    if template_type == "live_news":
        new_item["is_live"] = True
    elif template_type == "breaking_news":
        priority = input(f"{Fore.YELLOW}Enter priority (high/medium/low) [medium]: {Fore.WHITE}") or "medium"
        new_item["priority"] = priority
    
    config[template_type].append(new_item)
    
    if save_config(config):
        print(f"\n{Fore.GREEN}✓ News item added successfully! (ID: {new_id})")
    else:
        print(f"\n{Fore.RED}✗ Failed to save news item")

def edit_news(config, template_type, news_id):
    """Edit an existing news item"""
    if template_type not in config:
        print(f"{Fore.RED}Invalid template type: {template_type}")
        return
    
    # Find the news item
    news_item = None
    for item in config[template_type]:
        if item['id'] == news_id:
            news_item = item
            break
    
    if not news_item:
        print(f"{Fore.RED}News item with ID {news_id} not found!")
        return
    
    print(f"\n{Fore.GREEN}Editing news item (ID: {news_id})")
    print(f"{Fore.CYAN}{'='*60}\n")
    print(f"{Fore.YELLOW}Current Title: {Fore.WHITE}{news_item['title']}")
    print(f"{Fore.YELLOW}Current Content: {Fore.WHITE}{news_item['content'][:100]}...")
    
    # Edit title
    new_title = input(f"\n{Fore.YELLOW}Enter new title (press Enter to keep current): {Fore.WHITE}")
    if new_title:
        news_item['title'] = new_title
    
    # Edit content
    print(f"\n{Fore.YELLOW}Enter new content (press Enter twice to finish, or just Enter to keep current):")
    content_input = input()
    if content_input:
        content_lines = [content_input]
        while True:
            line = input()
            if line == "":
                break
            content_lines.append(line)
        news_item['content'] = "\n".join(content_lines).strip()
    
    # Edit timestamp
    new_timestamp = input(f"{Fore.YELLOW}Enter new timestamp (press Enter to keep current): {Fore.WHITE}")
    if new_timestamp:
        news_item['timestamp'] = new_timestamp
    
    if save_config(config):
        print(f"\n{Fore.GREEN}✓ News item updated successfully!")
    else:
        print(f"\n{Fore.RED}✗ Failed to update news item")

def delete_news(config, template_type, news_id):
    """Delete a news item"""
    if template_type not in config:
        print(f"{Fore.RED}Invalid template type: {template_type}")
        return
    
    # Find and remove the news item
    original_count = len(config[template_type])
    config[template_type] = [item for item in config[template_type] if item['id'] != news_id]
    
    if len(config[template_type]) < original_count:
        if save_config(config):
            print(f"\n{Fore.GREEN}✓ News item deleted successfully!")
        else:
            print(f"\n{Fore.RED}✗ Failed to delete news item")
    else:
        print(f"{Fore.RED}News item with ID {news_id} not found!")

def show_menu():
    """Display main menu"""
    print(f"\n{Fore.CYAN}{'='*60}")
    print(f"{Fore.GREEN}  NEWS TEMPLATE MANAGER")
    print(f"{Fore.CYAN}{'='*60}")
    print(f"{Fore.YELLOW}1. {Fore.WHITE}List news items")
    print(f"{Fore.YELLOW}2. {Fore.WHITE}Add news item")
    print(f"{Fore.YELLOW}3. {Fore.WHITE}Edit news item")
    print(f"{Fore.YELLOW}4. {Fore.WHITE}Delete news item")
    print(f"{Fore.YELLOW}5. {Fore.WHITE}Exit")
    print(f"{Fore.CYAN}{'='*60}\n")

def main():
    """Main function"""
    print(f"{Fore.GREEN}Loading news configuration...")
    config = load_config()
    
    while True:
        show_menu()
        choice = input(f"{Fore.YELLOW}Select an option: {Fore.WHITE}")
        
        if choice == "1":
            print(f"\n{Fore.CYAN}Available template types:")
            print(f"{Fore.WHITE}1. breaking_news")
            print(f"{Fore.WHITE}2. live_news")
            print(f"{Fore.WHITE}3. news_location")
            template_choice = input(f"\n{Fore.YELLOW}Select template type (1-3): {Fore.WHITE}")
            
            template_map = {"1": "breaking_news", "2": "live_news", "3": "news_location"}
            template_type = template_map.get(template_choice)
            
            if template_type:
                list_news(config, template_type)
            else:
                print(f"{Fore.RED}Invalid choice!")
        
        elif choice == "2":
            print(f"\n{Fore.CYAN}Available template types:")
            print(f"{Fore.WHITE}1. breaking_news")
            print(f"{Fore.WHITE}2. live_news")
            print(f"{Fore.WHITE}3. news_location")
            template_choice = input(f"\n{Fore.YELLOW}Select template type (1-3): {Fore.WHITE}")
            
            template_map = {"1": "breaking_news", "2": "live_news", "3": "news_location"}
            template_type = template_map.get(template_choice)
            
            if template_type:
                add_news(config, template_type)
                config = load_config()  # Reload config
            else:
                print(f"{Fore.RED}Invalid choice!")
        
        elif choice == "3":
            print(f"\n{Fore.CYAN}Available template types:")
            print(f"{Fore.WHITE}1. breaking_news")
            print(f"{Fore.WHITE}2. live_news")
            print(f"{Fore.WHITE}3. news_location")
            template_choice = input(f"\n{Fore.YELLOW}Select template type (1-3): {Fore.WHITE}")
            
            template_map = {"1": "breaking_news", "2": "live_news", "3": "news_location"}
            template_type = template_map.get(template_choice)
            
            if template_type:
                list_news(config, template_type)
                try:
                    news_id = int(input(f"\n{Fore.YELLOW}Enter news ID to edit: {Fore.WHITE}"))
                    edit_news(config, template_type, news_id)
                    config = load_config()  # Reload config
                except ValueError:
                    print(f"{Fore.RED}Invalid ID!")
            else:
                print(f"{Fore.RED}Invalid choice!")
        
        elif choice == "4":
            print(f"\n{Fore.CYAN}Available template types:")
            print(f"{Fore.WHITE}1. breaking_news")
            print(f"{Fore.WHITE}2. live_news")
            print(f"{Fore.WHITE}3. news_location")
            template_choice = input(f"\n{Fore.YELLOW}Select template type (1-3): {Fore.WHITE}")
            
            template_map = {"1": "breaking_news", "2": "live_news", "3": "news_location"}
            template_type = template_map.get(template_choice)
            
            if template_type:
                list_news(config, template_type)
                try:
                    news_id = int(input(f"\n{Fore.YELLOW}Enter news ID to delete: {Fore.WHITE}"))
                    confirm = input(f"{Fore.RED}Are you sure? (yes/no): {Fore.WHITE}")
                    if confirm.lower() == "yes":
                        delete_news(config, template_type, news_id)
                        config = load_config()  # Reload config
                except ValueError:
                    print(f"{Fore.RED}Invalid ID!")
            else:
                print(f"{Fore.RED}Invalid choice!")
        
        elif choice == "5":
            print(f"\n{Fore.GREEN}Goodbye!")
            break
        
        else:
            print(f"{Fore.RED}Invalid choice! Please select 1-5.")
        
        input(f"\n{Fore.CYAN}Press Enter to continue...")

if __name__ == "__main__":
    main()

