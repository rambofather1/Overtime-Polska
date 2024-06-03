import fetch from 'node-fetch';

export default async function handler(req, res) {
    try {
        const response = await fetch('http://45.43.163.166:25778/usercount');
        const data = await response.json();
        res.status(200).json(data);
    } catch (error) {
        console.error('Error fetching user count:', error);
        res.status(500).json({ error: 'Failed to fetch user count' });
    }
}
