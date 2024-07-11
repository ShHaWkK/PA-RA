class Ticket:
    def __init__(self, ticket_id, type, description, status, created_by, assigned_to=None):
        self.ticket_id = ticket_id
        self.type = type
        self.description = description
        self.status = status
        self.created_by = created_by
        self.assigned_to = assigned_to

    def to_dict(self):
        return {
            "id": self.ticket_id,
            "type": self.type,
            "description": self.description,
            "status": self.status,
            "created_by": self.created_by,
            "assigned_to": self.assigned_to
        }
