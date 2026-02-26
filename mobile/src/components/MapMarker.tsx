import React from 'react';
import {View, StyleSheet} from 'react-native';
import {Text} from 'react-native-paper';

type Props = {
  label: string;
  type: 'origen' | 'destino';
};

export default function MapMarker({label, type}: Props) {
  return (
    <View
      style={[
        styles.marker,
        {backgroundColor: type === 'origen' ? '#4caf50' : '#f44336'},
      ]}>
      <Text style={styles.text}>{label}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  marker: {
    padding: 6,
    borderRadius: 8,
    minWidth: 40,
    alignItems: 'center',
  },
  text: {
    color: '#fff',
    fontSize: 12,
    fontWeight: 'bold',
  },
});
