<?php
class DashboardStats {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserStats($user_id, $user_type) {
        switch ($user_type) {
            case 'buyer':
                return $this->getBuyerStats($user_id);
            case 'seller':
                return $this->getSellerStats($user_id);
            case 'agent':
                return $this->getAgentStats($user_id);
            case 'admin':
                return $this->getAdminStats();
            default:
                return $this->getBuyerStats($user_id);
        }
    }

    private function getBuyerStats($user_id) {
        $stats = [];

        // Total favorites
        $query = "SELECT COUNT(*) as count FROM favorites WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-heart',
            'value' => $result['count'],
            'label' => 'Favorite Properties'
        ];

        // Total inquiries
        $query = "SELECT COUNT(*) as count FROM inquiries WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-envelope',
            'value' => $result['count'],
            'label' => 'Property Inquiries'
        ];

        // Upcoming appointments
        $query = "SELECT COUNT(*) as count FROM appointments 
                  WHERE user_id = ? AND appointment_date >= CURDATE() AND status = 'confirmed'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-calendar-check',
            'value' => $result['count'],
            'label' => 'Upcoming Viewings'
        ];

        // Properties viewed
        $query = "SELECT COUNT(DISTINCT property_id) as count FROM property_views WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-eye',
            'value' => $result['count'],
            'label' => 'Properties Viewed'
        ];

        return $stats;
    }

    private function getSellerStats($user_id) {
        $stats = [];

        // Total properties
        $query = "SELECT COUNT(*) as count FROM properties WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-building',
            'value' => $result['count'],
            'label' => 'Total Properties'
        ];

        // Active properties
        $query = "SELECT COUNT(*) as count FROM properties WHERE user_id = ? AND status = 'available'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-home',
            'value' => $result['count'],
            'label' => 'Active Listings'
        ];

        // Total inquiries
        $query = "SELECT COUNT(*) as count FROM inquiries i 
                  JOIN properties p ON i.property_id = p.id 
                  WHERE p.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-envelope',
            'value' => $result['count'],
            'label' => 'Total Inquiries'
        ];

        // Pending appointments
        $query = "SELECT COUNT(*) as count FROM appointments a 
                  JOIN properties p ON a.property_id = p.id 
                  WHERE p.user_id = ? AND a.status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-calendar',
            'value' => $result['count'],
            'label' => 'Pending Appointments'
        ];

        return $stats;
    }

    private function getAgentStats($user_id) {
        $stats = $this->getSellerStats($user_id); // Agents have similar stats to sellers
        
        // Add agent-specific stats
        $query = "SELECT COUNT(DISTINCT p.user_id) as count FROM properties p 
                  WHERE p.user_id != ? AND p.user_id IN (SELECT id FROM users WHERE user_type = 'seller')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stats[] = [
            'icon' => 'fas fa-users',
            'value' => $result['count'],
            'label' => 'Clients'
        ];

        return $stats;
    }

    private function getAdminStats() {
        $stats = [];

        // Total users
        $query = "SELECT COUNT(*) as count FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-users',
            'value' => $result['count'],
            'label' => 'Total Users'
        ];

        // Total properties
        $query = "SELECT COUNT(*) as count FROM properties";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-building',
            'value' => $result['count'],
            'label' => 'Total Properties'
        ];

        // Total inquiries
        $query = "SELECT COUNT(*) as count FROM inquiries";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-envelope',
            'value' => $result['count'],
            'label' => 'Total Inquiries'
        ];

        // Pending verifications
        $query = "SELECT COUNT(*) as count FROM users WHERE is_verified = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[] = [
            'icon' => 'fas fa-user-check',
            'value' => $result['count'],
            'label' => 'Pending Verifications'
        ];

        return $stats;
    }
}
?>